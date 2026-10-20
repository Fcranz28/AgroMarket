<?php

namespace App\Models;

use App\Modules\Identity\Models\User;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'order_id',
        'reason',
        'description',
        'evidence',
        'status',
        'admin_notes',
    ];

    protected $casts = [
        'evidence' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
