<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckDniRequest;
use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use OpenApi\Attributes as OA;

class AdminController extends Controller
{
    public function index()
    {
        $pendingFarmers = User::pendingVerification()->count();
        $totalFarmers = User::farmers()->count();
        $totalUsers = User::where('role', User::ROLE_USER)->count();

        // Mock data or real counts if models exist
        $totalProducts = \App\Models\Product::count();
        $totalOrders = \App\Models\Order::count();
        // Calculate total revenue from completed orders
        $totalRevenue = \App\Models\Order::where('status', 'delivered')->sum('total');

        return view('admin.dashboard', compact(
            'pendingFarmers',
            'totalFarmers',
            'totalUsers',
            'totalProducts',
            'totalOrders',
            'totalRevenue'
        ));
    }

    public function users(Request $request)
    {
        $query = User::withCount('reportsReceived');

        // Search
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Sort
        if ($request->get('sort') === 'reports_desc') {
            $query->orderBy('reports_received_count', 'desc');
        } else {
            $query->latest();
        }

        $users = $query->paginate(10);

        return view('admin.users', compact('users'));
    }

    public function verifyView(User $user)
    {
        return view('admin.users.verify', compact('user'));
    }

    #[OA\Post(
        path: '/admin/usuarios/consultar-dni',
        summary: 'Consultar un DNI en RENIEC',
        description: 'Solo admins. Con `Accept: application/json` responde JSON; si el DNI existe, lo guarda en el usuario.',
        security: [['session' => [], 'csrf' => []]],
        tags: ['Admin'],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(required: ['dni', 'user_id'], properties: [
            new OA\Property(property: 'dni', type: 'string', pattern: '^\d{8}$', example: '12345678'),
            new OA\Property(property: 'user_id', type: 'integer'),
        ])),
        responses: [
            new OA\Response(response: 200, description: 'Resultado de la consulta', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'success', type: 'boolean'),
                new OA\Property(property: 'message', type: 'string', nullable: true, example: 'No se encontraron datos para este DNI.'),
                new OA\Property(property: 'data', type: 'object', nullable: true, properties: [
                    new OA\Property(property: 'document_number', type: 'string'),
                    new OA\Property(property: 'first_name', type: 'string'),
                    new OA\Property(property: 'first_last_name', type: 'string'),
                    new OA\Property(property: 'second_last_name', type: 'string'),
                ]),
            ])),
            new OA\Response(ref: '#/components/responses/Unauthenticated', response: 401),
            new OA\Response(ref: '#/components/responses/Forbidden', response: 403),
            new OA\Response(ref: '#/components/responses/ValidationFailed', response: 422),
        ],
    )]
    public function checkDni(CheckDniRequest $request): JsonResponse|RedirectResponse
    {
        $token = config('services.decolecta.token');
        $user = User::findOrFail($request->validated('user_id'));

        if (blank($token)) {
            return $this->dniResult($request, false, 'La consulta de DNI no está configurada.');
        }

        try {
            $response = Http::baseUrl(config('services.decolecta.url'))
                ->withToken($token)
                ->acceptJson()
                ->timeout(10)
                ->get('/v1/reniec/dni', ['numero' => $request->validated('dni')]);
        } catch (ConnectionException $exception) {
            Log::warning('RENIEC lookup failed', ['error' => $exception->getMessage()]);

            return $this->dniResult($request, false, 'No se pudo conectar con el servicio de RENIEC. Intenta nuevamente.');
        }

        $data = $response->json();

        if (! $response->successful() || ! isset($data['document_number'])) {
            return $this->dniResult($request, false, 'No se encontraron datos para este DNI.');
        }

        $user->update(['dni' => $data['document_number']]);

        return $this->dniResult($request, true, data: $data);
    }

    /**
     * @param  array<string, mixed>|null  $data
     */
    private function dniResult(CheckDniRequest $request, bool $success, ?string $message = null, ?array $data = null): JsonResponse|RedirectResponse
    {
        $result = array_filter(['success' => $success, 'message' => $message, 'data' => $data], fn ($value) => $value !== null);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json($result);
        }

        return back()->with('apiResult', $result);
    }

    public function verifyFarmer(User $user, string $status): RedirectResponse
    {
        if (! in_array($status, [User::STATUS_APPROVED, User::STATUS_REJECTED], true)) {
            return back()->with('error', 'Estado inválido.');
        }

        if (! $user->isFarmer()) {
            return back()->with('error', 'Solo se pueden verificar cuentas de agricultor.');
        }

        $user->update(['verification_status' => $status]);

        return redirect()->route('admin.users')->with('success', 'Estado del agricultor actualizado correctamente.');
    }

    public function toggleBan(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user()) || $user->isAdmin()) {
            return back()->with('error', 'No puedes suspender a un administrador.');
        }

        $user->update(['is_banned' => ! $user->is_banned]);
        $status = $user->is_banned ? 'suspendido' : 'activado';

        return back()->with('success', "Usuario {$status} correctamente.");
    }
}
