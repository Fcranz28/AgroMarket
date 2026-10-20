<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class AddressController extends Controller
{
    #[OA\Get(
        path: '/addresses',
        summary: 'Mis direcciones',
        security: [['session' => []]],
        tags: ['Cliente'],
        responses: [
            new OA\Response(response: 200, description: 'Direcciones del usuario', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Address'))),
            new OA\Response(ref: '#/components/responses/Unauthenticated', response: 401),
        ],
    )]
    public function index()
    {
        $user = Auth::user();

        return response()->json($user->addresses);
    }

    #[OA\Post(
        path: '/addresses',
        summary: 'Agregar una dirección',
        description: 'Máximo 3 direcciones por usuario.',
        security: [['session' => [], 'csrf' => []]],
        tags: ['Cliente'],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(required: ['address'], properties: [
            new OA\Property(property: 'address', type: 'string', example: 'Av. Arequipa 1234, Lima'),
            new OA\Property(property: 'latitude', type: 'number', nullable: true, example: -12.085),
            new OA\Property(property: 'longitude', type: 'number', nullable: true, example: -77.035),
        ])),
        responses: [
            new OA\Response(response: 201, description: 'Dirección creada', content: new OA\JsonContent(ref: '#/components/schemas/Address')),
            new OA\Response(ref: '#/components/responses/Unauthenticated', response: 401),
            new OA\Response(response: 422, description: 'Datos inválidos o ya tiene 3 direcciones', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ],
    )]
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->addresses()->count() >= 3) {
            return response()->json(['message' => 'Solo puedes tener un máximo de 3 direcciones.'], 422);
        }

        $validated = $request->validate([
            'address' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $address = $user->addresses()->create($validated);

        return response()->json($address, 201);
    }

    #[OA\Delete(
        path: '/addresses/{address}',
        summary: 'Eliminar una dirección',
        security: [['session' => [], 'csrf' => []]],
        tags: ['Cliente'],
        parameters: [new OA\Parameter(name: 'address', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Dirección eliminada', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(ref: '#/components/responses/Unauthenticated', response: 401),
            new OA\Response(ref: '#/components/responses/Forbidden', response: 403),
            new OA\Response(ref: '#/components/responses/NotFound', response: 404),
        ],
    )]
    public function destroy(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $address->delete();

        return response()->json(['message' => 'Dirección eliminada.']);
    }
}
