<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReportRequest;
use App\Models\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function store(StoreReportRequest $request): JsonResponse
    {
        $evidencePaths = collect($request->file('evidence', []))
            ->map(fn (UploadedFile $file) => Storage::url($file->store('reports', 'public')))
            ->values()
            ->all();

        $report = Report::create([
            'user_id' => $request->user()->id,
            'order_id' => $request->integer('order_id'),
            'product_id' => $request->integer('product_id'),
            'reason' => $request->validated('reason'),
            'description' => $request->validated('description'),
            'evidence' => $evidencePaths,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Reporte enviado correctamente',
            'report' => $report->only(['id', 'status', 'created_at']),
        ]);
    }
}
