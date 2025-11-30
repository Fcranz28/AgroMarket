<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    public function index()
    {
        $reports = Report::with(['user', 'product.user', 'order'])
            ->latest()
            ->paginate(10);

        return view('admin.reports.index', compact('reports'));
    }

    public function show(Report $report)
    {
        $report->load(['user', 'product.user', 'order']);
        return response()->json($report);
    }

    public function updateStatus(Request $request, Report $report)
    {
        $request->validate([
            'status' => 'required|in:pending,reviewed,resolved,dismissed',
            'admin_notes' => 'nullable|string'
        ]);

        $report->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes
        ]);

        return response()->json(['message' => 'Reporte actualizado correctamente']);
    }
}
