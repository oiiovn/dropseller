<?php

namespace App\Models\Crm;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNotification extends Model
{
    protected $table = 'crm_user_notifications';

    protected $fillable = [
        'user_id',
        'type',
        'category',
        'title',
        'message',
        'action_url',
        'related_type',
        'related_id',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
