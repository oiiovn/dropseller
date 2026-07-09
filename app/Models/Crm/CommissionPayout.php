<?php

namespace App\Models\Crm;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommissionPayout extends Model
{
    use HasFactory;

    protected $table = 'crm_commission_payouts';

    protected $fillable = [
        'commission_id',
        'amount_jpy',
        'paid_at',
        'payment_method',
        'reference_code',
        'recorded_by',
        'note',
    ];

    protected $casts = [
        'amount_jpy' => 'decimal:2',
        'paid_at' => 'date',
    ];

    public function commission(): BelongsTo
    {
        return $this->belongsTo(Commission::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
