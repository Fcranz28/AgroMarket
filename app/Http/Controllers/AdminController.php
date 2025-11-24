<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $pendingFarmers = User::pendingVerification()->count();
        $totalFarmers = User::farmers()->count();
        $totalUsers = User::where('role', User::ROLE_USER)->count();

        return view('admin.dashboard', compact('pendingFarmers', 'totalFarmers', 'totalUsers'));
    }

    public function users()
    {
        $users = User::latest()->paginate(10);
        return view('admin.users', compact('users'));
    }

    public function verifyFarmer(User $user, $status)
    {
        if (!in_array($status, [User::STATUS_APPROVED, User::STATUS_REJECTED])) {
            return back()->with('error', 'Estado inválido.');
        }

        $user->update(['verification_status' => $status]);

        return back()->with('success', 'Estado del agricultor actualizado.');
    }

    public function toggleBan(User $user)
    {
        $user->update(['is_banned' => !$user->is_banned]);
        $status = $user->is_banned ? 'suspendido' : 'activado';
        return back()->with('success', "Usuario $status correctamente.");
    }
}
