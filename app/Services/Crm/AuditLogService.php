<?php

namespace App\Services\Crm;

use App\Models\Crm\AuditLog;
use App\Models\User;

class AuditLogService
{
    public function write(?User $actor, string $module, string $event, mixed $auditable, array $oldValues = [], array $newValues = []): void
    {
        AuditLog::create([
            'user_id' => $actor?->id,
            'module' => $module,
            'event' => $event,
            'auditable_type' => $auditable ? get_class($auditable) : null,
            'auditable_id' => $auditable?->id,
            'old_values' => empty($oldValues) ? null : $oldValues,
            'new_values' => empty($newValues) ? null : $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
