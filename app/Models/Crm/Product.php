<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'crm_products';

    protected $fillable = [
        'sku',
        'name',
        'category',
        'brand',
        'stock_quantity',
        'base_cost_jpy',
        'recommended_price_jpy',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'base_cost_jpy' => 'decimal:2',
        'recommended_price_jpy' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
