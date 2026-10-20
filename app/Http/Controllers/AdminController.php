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

    public function verifyFarmer(User $user, $status)
    {
        if (! in_array($status, [User::STATUS_APPROVED, User::STATUS_REJECTED])) {
            return back()->with('error', 'Estado inválido.');
        }

        $user->update(['verification_status' => $status]);

        return redirect()->route('admin.users')->with('success', 'Estado del agricultor actualizado correctamente.');
    }

    public function toggleBan(User $user)
    {
        $user->update(['is_banned' => ! $user->is_banned]);
        $status = $user->is_banned ? 'suspendido' : 'activado';

        return back()->with('success', "Usuario $status correctamente.");
    }
}
