<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * Tên bảng tương ứng trong database.
     */
    protected $table = 'products';

    /**
     * Các trường có thể được gán giá trị (Mass Assignable).
     */
    protected $fillable = [
        'sku',
        'price',
    ];
    public function order_detail()
    {
        return $this->hasMany(OrderDetail::class, 'sku', 'sku'); 
    }
}
