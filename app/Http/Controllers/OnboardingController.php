<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnboardingController extends Controller
{
    public function welcome()
    {
        return view('onboarding.welcome');
    }

    public function selectRole(Request $request)
    {
        $request->validate([
            'role' => 'required|in:user,farmer',
        ]);

        $user = Auth::user();
        $user->update(['role' => $request->role]);

        if ($request->role === 'farmer') {
            return redirect()->route('onboarding.farmer');
        }

        return redirect()->route('onboarding.user');
    }

    public function userPreferences()
    {
        $categories = \App\Models\Category::all();

        return view('onboarding.user', compact('categories'));
    }

    public function savePreferences(Request $request)
    {
        // Here we would save preferences (e.g., attach categories to user)
        // For now, we just complete onboarding

        $user = Auth::user();
        $user->update(['onboarding_completed' => true]);

        return redirect()->route('home')->with('success', '¡Bienvenido a AgroMarket!');
    }

    public function farmerVerification()
    {
        return view('onboarding.farmer');
    }

    public function saveVerification(Request $request)
    {
        $request->validate([
            'face_photo' => 'required|image|max:5120', // 5MB
            'dni_front' => 'required|image|max:5120',
            'dni_back' => 'required|image|max:5120',
        ]);

        $user = Auth::user();

        $facePath = $request->file('face_photo')->store('verification/faces', 'local');
        $dniFrontPath = $request->file('dni_front')->store('verification/dni', 'local');
        $dniBackPath = $request->file('dni_back')->store('verification/dni', 'local');

        $user->update([
            'face_photo' => $facePath,
            'dni_front' => $dniFrontPath,
            'dni_back' => $dniBackPath,
            'verification_status' => User::STATUS_PENDING,
            'onboarding_completed' => true,
        ]);

        return redirect()->route('farmer.dashboard')->with('success', '¡Solicitud enviada! Tu cuenta está en revisión.');
    }
}
