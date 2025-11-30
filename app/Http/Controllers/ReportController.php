<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'reason' => 'required|string|max:255',
            'description' => 'required|string',
            'evidence.*' => 'nullable|image|max:2048' // Max 2MB per image
        ]);

        $evidencePaths = [];
        if ($request->hasFile('evidence')) {
            foreach ($request->file('evidence') as $file) {
                // Store in 'reports' directory in public disk
                $path = $file->store('reports', 'public');
                $evidencePaths[] = Storage::url($path);
            }
        }

        $report = Report::create([
            'user_id' => Auth::id(),
            'order_id' => $request->order_id,
            'product_id' => $request->product_id,
            'reason' => $request->reason,
            'description' => $request->description,
            'evidence' => $evidencePaths,
            'status' => 'pending'
        ]);

        return response()->json([
            'message' => 'Reporte enviado correctamente',
            'report' => $report
        ]);
    }
}
