<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DebtCreditorActivityLog extends Model
{
    public $timestamps = false;

    protected $table = 'debt_creditor_activity_logs';

    protected $fillable = [
        'user_id',
        'action',
        'route_name',
        'path',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public const ACTION_LOGIN = 'login';
    public const ACTION_LOGIN_FAILED = 'login_failed';
    public const ACTION_VIEW_PAGE = 'view_page';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function logLogin(Request $request): void
    {
        $user = $request->user();
        if (!$user) {
            return;
        }
        self::create([
            'user_id' => $user->id,
            'action' => self::ACTION_LOGIN,
            'route_name' => 'debt.code.verify.post',
            'path' => $request->path(),
            'ip_address' => $request->ip() ?? '',
            'user_agent' => $request->userAgent(),
        ]);
    }

    public static function logLoginFailed(Request $request): void
    {
        $user = $request->user();
        if (!$user) {
            return;
        }
        self::create([
            'user_id' => $user->id,
            'action' => self::ACTION_LOGIN_FAILED,
            'route_name' => 'debt.code.verify.post',
            'path' => $request->path(),
            'ip_address' => $request->ip() ?? '',
            'user_agent' => $request->userAgent(),
        ]);
    }

    public static function logViewPage(Request $request): void
    {
        $user = $request->user();
        if (!$user || !$user->isDebtSystemUser()) {
            return;
        }
        $route = $request->route();
        if ($route && $route->getName() === 'debt.code.verify.post') {
            return; // Đã ghi log login / login_failed trong controller
        }
        self::create([
            'user_id' => $user->id,
            'action' => self::ACTION_VIEW_PAGE,
            'route_name' => $route ? $route->getName() : null,
            'path' => $request->path(),
            'ip_address' => $request->ip() ?? '',
            'user_agent' => $request->userAgent(),
        ]);
    }
}
