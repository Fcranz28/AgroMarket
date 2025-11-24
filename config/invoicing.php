<?php

return [
    /*
    |--------------------------------------------------------------------------
    | APIs Peru Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for APIs Peru electronic invoicing service
    | NOTE: This is configured for SANDBOX/TEST environment
    | Update endpoint and credentials for production use
    |
    */

    'apis_peru' => [
        'token' => env('APIS_PERU_TOKEN'),
        'endpoint' => env('APIS_PERU_ENDPOINT', 'https://facturacion.apisperu.com/api/v1/invoice/send'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Company Information
    |--------------------------------------------------------------------------
    |
    | Your company information for invoice generation
    |
    */

    'company' => [
        'ruc' => env('COMPANY_RUC', '20000000001'),
        'razon_social' => env('COMPANY_RAZON_SOCIAL', 'MI EMPRESA S.A.C.'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Invoice Series
    |--------------------------------------------------------------------------
    |
    | Series prefixes for different invoice types
    |
    */

    'series' => [
        'factura' => env('INVOICE_SERIE_FACTURA', 'F001'),
        'boleta' => env('INVOICE_SERIE_BOLETA', 'B001'),
    ],
];
