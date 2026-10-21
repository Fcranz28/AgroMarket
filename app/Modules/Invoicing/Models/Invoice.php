<?php

namespace App\Modules\Invoicing\Models;

use App\Modules\Invoicing\Database\Factories\InvoiceFactory;
use App\Modules\Invoicing\Policies\InvoicePolicy;
use App\Modules\Orders\Models\Order;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UseFactory(InvoiceFactory::class)]
#[UsePolicy(InvoicePolicy::class)]
class Invoice extends Model
{
    use HasFactory, HasUuids;

    /**
     * The id stays numeric; the uuid column is generated on create and used in URLs.
     *
     * @return list<string>
     */
    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

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
     *
     * @return BelongsTo<Order, $this>
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Path of the generated invoice JSON on the private disk.
     */
    public function jsonPath(): string
    {
        return "invoices/{$this->serie}-{$this->correlativo}.json";
    }

    /**
     * Get the formatted invoice type name
     */
    public function getTipoNombreAttribute()
    {
        return match ($this->tipo_doc) {
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
