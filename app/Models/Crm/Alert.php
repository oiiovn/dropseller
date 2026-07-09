<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Alert extends Model
{
    use HasFactory;

    protected $table = 'crm_alerts';

    protected $fillable = [
        'type',
        'severity',
        'title',
        'message',
        'related_type',
        'related_id',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function related(): MorphTo
    {
        return $this->morphTo();
    }
}
