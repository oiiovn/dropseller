<?php

namespace App\Models\Crm;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Commission extends Model
{
    use HasFactory;

    protected $table = 'crm_commissions';

    protected $fillable = [
        'order_id',
        'affiliate_id',
        'commission_rule_id',
        'commission_rate',
        'commission_amount_jpy',
        'applied_rule_name',
        'approval_status',
        'payment_status',
        'wallet_status',
        'completed_at',
        'credited_at',
        'settlement_id',
        'clawback_for_commission_id',
        'approved_at',
        'paid_at',
        'approved_by',
        'paid_by',
        'notes',
    ];

    protected $casts = [
        'commission_rate' => 'decimal:2',
        'commission_amount_jpy' => 'decimal:2',
        'approved_at' => 'date',
        'paid_at' => 'date',
        'completed_at' => 'datetime',
        'credited_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function commissionRule(): BelongsTo
    {
        return $this->belongsTo(CommissionRule::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(CommissionPayout::class);
    }

    public function settlement(): BelongsTo
    {
        return $this->belongsTo(CommissionSettlement::class, 'settlement_id');
    }

    public function clawbackFor(): BelongsTo
    {
        return $this->belongsTo(self::class, 'clawback_for_commission_id');
    }
}
