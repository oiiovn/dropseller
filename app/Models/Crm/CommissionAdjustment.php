<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommissionAdjustment extends Model
{
    use HasFactory;

    protected $table = 'crm_commission_adjustments';

    protected $fillable = [
        'affiliate_id',
        'commission_id',
        'amount_jpy',
        'reason',
        'wallet_status',
    ];

    protected $casts = [
        'amount_jpy' => 'decimal:2',
    ];

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function commission(): BelongsTo
    {
        return $this->belongsTo(Commission::class);
    }
}
