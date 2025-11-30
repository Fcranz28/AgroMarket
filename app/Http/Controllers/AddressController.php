<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return response()->json($user->addresses);
    }

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

    public function destroy(Address $address)
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $address->delete();

        return response()->json(['message' => 'Dirección eliminada.']);
    }
}
