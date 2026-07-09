<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $table = 'crm_order_items';

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'unit_price_jpy',
        'unit_cost_jpy',
        'line_total_jpy',
        'line_profit_jpy',
        'notes',
    ];

    protected $casts = [
        'unit_price_jpy' => 'decimal:2',
        'unit_cost_jpy' => 'decimal:2',
        'line_total_jpy' => 'decimal:2',
        'line_profit_jpy' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
