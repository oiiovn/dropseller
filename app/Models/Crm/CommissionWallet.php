<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommissionWallet extends Model
{
    use HasFactory;

    protected $table = 'crm_commission_wallets';

    protected $fillable = [
        'affiliate_id',
        'unpaid_balance_jpy',
        'paid_balance_jpy',
        'total_commission_jpy',
    ];

    protected $casts = [
        'unpaid_balance_jpy' => 'decimal:2',
        'paid_balance_jpy' => 'decimal:2',
        'total_commission_jpy' => 'decimal:2',
    ];

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }
}
