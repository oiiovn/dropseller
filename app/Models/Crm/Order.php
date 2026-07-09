<?php

namespace App\Models\Crm;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'crm_orders';

    protected $fillable = [
        'order_code',
        'customer_id',
        'affiliate_id',
        'created_by',
        'order_status',
        'payment_status',
        'debt_status',
        'subtotal_jpy',
        'discount_jpy',
        'shipping_fee_jpy',
        'total_amount_jpy',
        'total_cost_jpy',
        'total_profit_jpy',
        'confirmed_at',
        'notes',
        'facebook_url',
        'battery_capacity',
        'payment_method',
        'shipping_mode',
        'delivery_date',
        'delivery_time_slot',
        'delivery_date_from',
        'delivery_date_to',
        'vehicle_chassis_number',
        'vehicle_owner_name_vi',
        'vehicle_owner_name_ja',
        'vehicle_owner_postal_code',
        'vehicle_owner_phone',
        'vehicle_owner_address',
    ];

    protected $casts = [
        'subtotal_jpy' => 'decimal:2',
        'discount_jpy' => 'decimal:2',
        'shipping_fee_jpy' => 'decimal:2',
        'total_amount_jpy' => 'decimal:2',
        'total_cost_jpy' => 'decimal:2',
        'total_profit_jpy' => 'decimal:2',
        'confirmed_at' => 'date',
        'delivery_date' => 'date',
        'delivery_date_from' => 'date',
        'delivery_date_to' => 'date',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->latest();
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function debt(): HasOne
    {
        return $this->hasOne(Debt::class);
    }

    public function delivery(): HasOne
    {
        return $this->hasOne(Delivery::class);
    }

    public function commission(): HasOne
    {
        return $this->hasOne(Commission::class);
    }

    public function auditLogs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable')->latest();
    }
}
