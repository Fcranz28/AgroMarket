<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

/**
 * Global OpenAPI definitions (info, security and shared components) read by L5-Swagger.
 *
 * Only endpoints that return JSON are documented; HTML pages are not part of the API.
 */
#[OA\Info(
    version: '1.0.0',
    title: 'AgroMarket API',
    description: "Endpoints JSON de AgroMarket.\n\nLos endpoints marcados con candado usan la **sesión web** de Laravel (cookie de sesión) y, si no son GET, el token **CSRF** en la cabecera `X-CSRF-TOKEN` (está en `<meta name=\"csrf-token\">` de cada página). Los precios están en soles (PEN) e **incluyen IGV**.",
)]
#[OA\Server(url: L5_SWAGGER_CONST_HOST, description: 'Servidor de la aplicación')]
#[OA\Tag(name: 'Catálogo', description: 'Productos y búsqueda (públicos)')]
#[OA\Tag(name: 'Pagos', description: 'Checkout con Stripe (también para invitados)')]
#[OA\Tag(name: 'Autenticación', description: 'Login social con Firebase')]
#[OA\Tag(name: 'Cliente', description: 'Direcciones, reportes y facturas del usuario logueado')]
#[OA\Tag(name: 'Agricultor', description: 'Gestión de pedidos del agricultor')]
#[OA\Tag(name: 'Admin', description: 'Herramientas del administrador')]
#[OA\Components(
    schemas: [
        new OA\Schema(
            schema: 'CatalogProduct',
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 12),
                new OA\Property(property: 'category_id', type: 'integer', example: 1),
                new OA\Property(property: 'name', type: 'string', example: 'Papa amarilla'),
                new OA\Property(property: 'slug', type: 'string', example: 'papa-amarilla-a1b2c3d4'),
                new OA\Property(property: 'price', type: 'string', example: '4.50', description: 'Precio de la unidad más barata, IGV incluido'),
                new OA\Property(property: 'unit', type: 'string', example: 'kg'),
                new OA\Property(property: 'stock', type: 'integer', example: 120),
                new OA\Property(property: 'image_path', type: 'string', nullable: true, example: 'products/papa.jpg', description: 'Ruta relativa al disco público (/storage/...)'),
            ],
        ),
        new OA\Schema(
            schema: 'SearchProduct',
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 12),
                new OA\Property(property: 'slug', type: 'string', example: 'papa-amarilla-a1b2c3d4'),
                new OA\Property(property: 'name', type: 'string', example: 'Papa amarilla'),
                new OA\Property(property: 'price', type: 'string', example: '4.50'),
                new OA\Property(property: 'unit', type: 'string', example: 'kg'),
                new OA\Property(property: 'image_path', type: 'string', format: 'uri', nullable: true, description: 'URL absoluta de la imagen'),
                new OA\Property(property: 'image_url', type: 'string', format: 'uri', description: 'URL de la imagen o del placeholder'),
                new OA\Property(property: 'category_name', type: 'string', nullable: true, example: 'Tubérculos'),
            ],
        ),
        new OA\Schema(
            schema: 'CartLine',
            required: ['id', 'cantidad'],
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 12, description: 'ID del producto'),
                new OA\Property(property: 'unit_id', type: 'integer', nullable: true, example: 34, description: 'ID de la unidad (kg, saco...). Sin unidad se cobra la más barata.'),
                new OA\Property(property: 'cantidad', type: 'integer', minimum: 1, maximum: 1000, example: 2),
            ],
        ),
        new OA\Schema(
            schema: 'Address',
            properties: [
                new OA\Property(property: 'id', type: 'integer', example: 5),
                new OA\Property(property: 'user_id', type: 'integer', example: 3),
                new OA\Property(property: 'address', type: 'string', example: 'Av. Arequipa 1234, Lima'),
                new OA\Property(property: 'latitude', type: 'string', nullable: true, example: '-12.08500000'),
                new OA\Property(property: 'longitude', type: 'string', nullable: true, example: '-77.03500000'),
                new OA\Property(property: 'is_default', type: 'boolean', example: false),
            ],
        ),
        new OA\Schema(
            schema: 'Error',
            properties: [new OA\Property(property: 'error', type: 'string', example: 'El pago no coincide con el contenido del carrito.')],
        ),
        new OA\Schema(
            schema: 'Message',
            properties: [new OA\Property(property: 'message', type: 'string')],
        ),
        new OA\Schema(
            schema: 'ValidationError',
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Tu carrito está vacío.'),
                new OA\Property(
                    property: 'errors',
                    type: 'object',
                    additionalProperties: new OA\AdditionalProperties(type: 'array', items: new OA\Items(type: 'string')),
                    example: ['cart' => ['Tu carrito está vacío.']],
                ),
            ],
        ),
    ],
    responses: [
        new OA\Response(response: 'Unauthenticated', description: 'Sin sesión iniciada', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
        new OA\Response(response: 'Forbidden', description: 'Sin permiso para este recurso', content: new OA\JsonContent(ref: '#/components/schemas/Message')),
        new OA\Response(response: 'NotFound', description: 'Recurso no encontrado'),
        new OA\Response(response: 'ValidationFailed', description: 'Datos inválidos', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        new OA\Response(response: 'TooManyRequests', description: 'Demasiadas solicitudes (rate limit)'),
    ],
    parameters: [
        new OA\Parameter(
            parameter: 'sort',
            name: 'sort',
            in: 'query',
            required: false,
            description: 'Orden de los resultados (por defecto, los más nuevos)',
            schema: new OA\Schema(type: 'string', enum: ['featured', 'price-asc', 'price-desc', 'name-asc', 'name-desc']),
        ),
    ],
    securitySchemes: [
        new OA\SecurityScheme(
            securityScheme: 'session',
            type: 'apiKey',
            in: 'cookie',
            name: 'agromarket_session',
            description: 'Cookie de sesión que se obtiene al iniciar sesión en la web.',
        ),
        new OA\SecurityScheme(
            securityScheme: 'csrf',
            type: 'apiKey',
            in: 'header',
            name: 'X-CSRF-TOKEN',
            description: 'Token CSRF de la página (meta csrf-token). Obligatorio en POST, PUT, PATCH y DELETE.',
        ),
    ],
)]
class OpenApiSpec {}
