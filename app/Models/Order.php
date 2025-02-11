<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders'; // Tên bảng trong cơ sở dữ liệu

    protected $fillable = [
        'order_code',
        'export_date',
        'filter_date',
        'shop_id',
        'total_products',
        'total_dropship',
        'total_bill',
        'payment_status',
        'transaction_id',
        'reconciled',
        'created_at',
        'updated_at',
    ];

    /**
     * Quan hệ với bảng order_details
     */
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'id');
    }
    

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id', 'shop_id'); // Trường kết nối shop_id
    }
    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'transaction_id', 'transaction_id');
    }
    public function notification()
    {
        return $this->hasOne(Notification::class, 'order_id', 'id'); // Một Order có một Notification
    }
    
}

