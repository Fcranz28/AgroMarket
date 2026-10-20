<?php

namespace App\Modules\Moderation\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Moderation\Http\Requests\StoreReportRequest;
use App\Modules\Moderation\Models\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use OpenApi\Attributes as OA;

class ReportController extends Controller
{
    #[OA\Post(
        path: '/reportes',
        summary: 'Reportar un producto de un pedido propio',
        security: [['session' => [], 'csrf' => []]],
        tags: ['Cliente'],
        requestBody: new OA\RequestBody(required: true, content: new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(required: ['order_id', 'product_id', 'reason', 'description'], properties: [
                new OA\Property(property: 'order_id', type: 'integer', description: 'Pedido del usuario que contiene el producto'),
                new OA\Property(property: 'product_id', type: 'integer'),
                new OA\Property(property: 'reason', type: 'string', maxLength: 255, example: 'Producto en mal estado'),
                new OA\Property(property: 'description', type: 'string', maxLength: 2000),
                new OA\Property(property: 'evidence[]', type: 'array', maxItems: 5, items: new OA\Items(type: 'string', format: 'binary'), description: 'Imágenes JPG/PNG/WEBP de hasta 2 MB'),
            ]),
        )),
        responses: [
            new OA\Response(response: 200, description: 'Reporte enviado', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Reporte enviado correctamente'),
                new OA\Property(property: 'report', properties: [
                    new OA\Property(property: 'id', type: 'integer'),
                    new OA\Property(property: 'status', type: 'string', example: 'pending'),
                    new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                ], type: 'object'),
            ])),
            new OA\Response(ref: '#/components/responses/Unauthenticated', response: 401),
            new OA\Response(ref: '#/components/responses/ValidationFailed', response: 422),
        ],
    )]
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
