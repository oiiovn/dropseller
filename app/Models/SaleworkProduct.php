<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleworkProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_code',
        'product_name',
        'unit',
        'tax_rate',
        'import_price',
        'wholesale_price',
        'retail_price',
        'barcode',
        'stock',
        'reserved_stock',
        'image_url',
        'category'
    ];

    protected $casts = [
        'tax_rate' => 'decimal:2',
        'import_price' => 'decimal:2',
        'wholesale_price' => 'decimal:2',
        'retail_price' => 'decimal:2',
        'stock' => 'integer',
        'reserved_stock' => 'integer'
    ];

    // Scope để tìm sản phẩm theo mã
    public function scopeByCode($query, $code)
    {
        return $query->where('product_code', $code);
    }

    // Scope để tìm sản phẩm có danh mục
    public function scopeWithCategory($query)
    {
        return $query->whereNotNull('category');
    }
}
