<?php

namespace App\Modules\Identity\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Identity\Http\Requests\FarmerVerificationRequest;
use App\Modules\Identity\Http\Requests\SelectRoleRequest;
use App\Modules\Identity\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    public function welcome(): View
    {
        return view('onboarding.welcome');
    }

    public function selectRole(SelectRoleRequest $request): RedirectResponse
    {
        $role = $request->validated('role');

        $request->user()->forceFill([
            'role' => $role,
            'verification_status' => User::STATUS_NONE,
        ])->save();

        return $role === User::ROLE_FARMER
            ? redirect()->route('onboarding.farmer')
            : redirect()->route('onboarding.user');
    }

    /**
     * The category list is provided by the Catalog module (view composer).
     */
    public function userPreferences(): View
    {
        return view('onboarding.user');
    }

    public function savePreferences(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->isFarmer()) {
            return redirect()->route('onboarding.farmer');
        }

        $user->update(['onboarding_completed' => true]);

        return redirect()->route('home')->with('success', '¡Bienvenido a AgroMarket!');
    }

    public function farmerVerification(Request $request): View|RedirectResponse
    {
        if (! $request->user()->isFarmer()) {
            return redirect()->route('onboarding.welcome');
        }

        return view('onboarding.farmer');
    }

    public function saveVerification(FarmerVerificationRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->update([
            'face_photo' => $request->file('face_photo')->store('verification/faces', 'local'),
            'dni_front' => $request->file('dni_front')->store('verification/dni', 'local'),
            'dni_back' => $request->file('dni_back')->store('verification/dni', 'local'),
            'verification_status' => User::STATUS_PENDING,
            'onboarding_completed' => true,
        ]);

        return redirect()->route('farmer.dashboard')->with('success', '¡Solicitud enviada! Tu cuenta está en revisión.');
    }
}
