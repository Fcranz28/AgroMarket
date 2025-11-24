<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Update user profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'dni' => 'nullable|string|max:8',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048'
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists and it's not an OAuth URL
            if ($user->avatar && !filter_var($user->avatar, FILTER_VALIDATE_URL)) {
                \Storage::delete('public/' . $user->avatar);
            }
            
            // Store new avatar
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        $user->update($validated);

        return redirect()->route('orders.index')
            ->with('success', 'Perfil actualizado correctamente');
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = Auth::user();

        // Verify current password
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors([
                'current_password' => 'La contraseña actual no es correcta'
            ]);
        }

        // Update password
        $user->update([
            'password' => Hash::make($validated['password'])
        ]);

        return redirect()->route('orders.index')
            ->with('success', 'Contraseña actualizada correctamente');
    }
}
