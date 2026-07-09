<?php

namespace App\Models\Crm;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Affiliate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'full_name',
        'phone',
        'email',
        'area',
        'region',
        'status',
        'total_referred_customers',
        'successful_orders',
        'gross_sales_jpy',
        'total_commission_jpy',
        'paid_commission_jpy',
        'pending_commission_jpy',
        'joined_at',
        'notes',
    ];

    protected $casts = [
        'gross_sales_jpy' => 'decimal:2',
        'total_commission_jpy' => 'decimal:2',
        'paid_commission_jpy' => 'decimal:2',
        'pending_commission_jpy' => 'decimal:2',
        'joined_at' => 'date',
    ];

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function commissionRules(): BelongsToMany
    {
        return $this->belongsToMany(CommissionRule::class, 'crm_commission_rule_affiliate');
    }

    public function commissionWallet(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(CommissionWallet::class);
    }

    public function commissionSettlements(): HasMany
    {
        return $this->hasMany(CommissionSettlement::class);
    }
}
