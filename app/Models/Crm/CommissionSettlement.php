<?php

namespace App\Models\Crm;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommissionSettlement extends Model
{
    use HasFactory;

    protected $table = 'crm_commission_settlements';

    protected $fillable = [
        'affiliate_id',
        'period_month',
        'total_amount_jpy',
        'status',
        'paid_at',
        'paid_by',
        'note',
    ];

    protected $casts = [
        'total_amount_jpy' => 'decimal:2',
        'paid_at' => 'date',
    ];

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class, 'settlement_id');
    }
}
