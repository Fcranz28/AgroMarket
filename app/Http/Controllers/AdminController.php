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
            $query->where(function($q) use ($search) {
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

    public function checkDni(Request $request)
    {
        $request->validate([
            'dni' => 'required|numeric|digits:8',
            'user_id' => 'required|exists:users,id'
        ]);

        $token = 'sk_12002.IKm739baMvp9BDi3QZOPiPlJS3EH5b8q';
        $dni = $request->input('dni');
        $user = User::find($request->input('user_id'));

        try {
            $client = new \GuzzleHttp\Client(['base_uri' => 'https://api.decolecta.com', 'verify' => false]);
            
            $response = $client->request('GET', '/v1/reniec/dni', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'query' => ['numero' => $dni]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            // Update user's DNI if successful
            if ($data && isset($data['document_number'])) {
                $user->update(['dni' => $data['document_number']]);
                
                // Check if request is AJAX
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['success' => true, 'data' => $data]);
                }
                
                return back()->with('apiResult', ['success' => true, 'data' => $data]);
            } else {
                $message = 'No se encontraron datos para este DNI.';
                
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => $message]);
                }
                
                return back()->with('apiResult', ['success' => false, 'message' => $message]);
            }

        } catch (\Exception $e) {
            $message = 'Error al consultar RENIEC: ' . $e->getMessage();
            
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $message]);
            }
            
            return back()->with('apiResult', ['success' => false, 'message' => $message]);
        }
    }

    public function verifyFarmer(User $user, $status)
    {
        if (!in_array($status, [User::STATUS_APPROVED, User::STATUS_REJECTED])) {
            return back()->with('error', 'Estado inválido.');
        }

        $user->update(['verification_status' => $status]);

        return redirect()->route('admin.users')->with('success', 'Estado del agricultor actualizado correctamente.');
    }

    public function toggleBan(User $user)
    {
        $user->update(['is_banned' => !$user->is_banned]);
        $status = $user->is_banned ? 'suspendido' : 'activado';
        return back()->with('success', "Usuario $status correctamente.");
    }
}
