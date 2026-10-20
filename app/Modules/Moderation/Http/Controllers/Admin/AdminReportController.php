<?php

namespace App\Modules\Moderation\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Moderation\Models\Report;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class AdminReportController extends Controller
{
    public function index()
    {
        $reports = Report::with(['user', 'product.user', 'order'])
            ->latest()
            ->paginate(10);

        return view('admin.reports.index', compact('reports'));
    }

    #[OA\Get(
        path: '/admin/reportes/{report}',
        summary: 'Detalle de un reporte',
        security: [['session' => []]],
        tags: ['Admin'],
        parameters: [new OA\Parameter(name: 'report', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Reporte con usuario, producto (y su agricultor) y pedido', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'id', type: 'integer'),
                new OA\Property(property: 'reason', type: 'string'),
                new OA\Property(property: 'description', type: 'string'),
                new OA\Property(property: 'evidence', type: 'array', items: new OA\Items(type: 'string', format: 'uri')),
                new OA\Property(property: 'status', type: 'string', enum: ['pending', 'reviewed', 'resolved', 'dismissed']),
                new OA\Property(property: 'admin_notes', type: 'string', nullable: true),
                new OA\Property(property: 'user', type: 'object'),
                new OA\Property(property: 'product', type: 'object'),
                new OA\Property(property: 'order', type: 'object'),
            ])),
            new OA\Response(ref: '#/components/responses/Unauthenticated', response: 401),
            new OA\Response(ref: '#/components/responses/Forbidden', response: 403),
            new OA\Response(ref: '#/components/responses/NotFound', response: 404),
        ],
    )]
    public function show(Report $report)
    {
        $report->load(['user', 'product.user', 'order']);

        return response()->json($report);
    }

    #[OA\Patch(
        path: '/admin/reportes/{report}/status',
        summary: 'Cambiar el estado de un reporte',
        security: [['session' => [], 'csrf' => []]],
        tags: ['Admin'],
        parameters: [new OA\Parameter(name: 'report', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(required: ['status'], properties: [
            new OA\Property(property: 'status', type: 'string', enum: ['pending', 'reviewed', 'resolved', 'dismissed']),
            new OA\Property(property: 'admin_notes', type: 'string', nullable: true),
        ])),
        responses: [
            new OA\Response(response: 200, description: 'Reporte actualizado', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
            new OA\Response(ref: '#/components/responses/Unauthenticated', response: 401),
            new OA\Response(ref: '#/components/responses/Forbidden', response: 403),
            new OA\Response(ref: '#/components/responses/ValidationFailed', response: 422),
        ],
    )]
    public function updateStatus(Request $request, Report $report)
    {
        $request->validate([
            'status' => 'required|in:pending,reviewed,resolved,dismissed',
            'admin_notes' => 'nullable|string',
        ]);

        $report->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
        ]);

        return response()->json(['message' => 'Reporte actualizado correctamente']);
    }
}
