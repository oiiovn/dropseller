<?php

namespace App\Models\Crm;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentHistory extends Model
{
    use HasFactory;

    protected $table = 'crm_payment_histories';

    protected $fillable = [
        'payment_id',
        'from_status',
        'to_status',
        'changed_by',
        'note',
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function changer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
