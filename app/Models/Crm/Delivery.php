<?php

namespace App\Models\Crm;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Delivery extends Model
{
    use HasFactory;

    protected $table = 'crm_deliveries';

    protected $fillable = [
        'order_id',
        'delivery_address',
        'scheduled_at',
        'assigned_user_id',
        'delivery_status',
        'shipping_fee_jpy',
        'delivered_at',
        'note',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'delivered_at' => 'datetime',
        'shipping_fee_jpy' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }
}
