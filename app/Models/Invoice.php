<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'tipo_doc',
        'serie',
        'correlativo',
        'numero_completo',
        'subtotal',
        'igv',
        'total',
        'api_request',
        'api_response',
        'pdf_url',
        'xml_url',
        'cdr_url',
        'status',
        'error_message',
    ];

    protected $casts = [
        'api_request' => 'array',
        'api_response' => 'array',
        'subtotal' => 'decimal:2',
        'igv' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Get the order that owns the invoice
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the formatted invoice type name
     */
    public function getTipoNombreAttribute()
    {
        return match($this->tipo_doc) {
            '01' => 'Factura',
            '03' => 'Boleta',
            default => 'Comprobante',
        };
    }

    /**
     * Check if invoice was successfully generated
     */
    public function isSuccess()
    {
        return $this->status === 'success';
    }

    /**
     * Check if invoice has errors
     */
    public function hasError()
    {
        return $this->status === 'error';
    }

    /**
     * Check if invoice is pending
     */
    public function isPending()
    {
        return in_array($this->status, ['pending', 'processing']);
    }
}
