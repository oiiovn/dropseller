<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Crm\UserNotification;
use App\Services\Crm\CrmNotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CrmNotificationController extends Controller
{
    public function __construct(private readonly CrmNotificationService $notificationService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $limit = max(1, min((int) $request->input('limit', 15), 50));

        return response()->json([
            'notifications' => $this->notificationService->listForUser(
                $request->user(),
                $limit,
                $request->boolean('unread_only')
            ),
            'unread_count' => $this->notificationService->unreadCount($request->user()),
        ]);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        return response()->json([
            'unread_count' => $this->notificationService->unreadCount($request->user()),
        ]);
    }

    public function markRead(Request $request, UserNotification $notification): JsonResponse
    {
        $notification = $this->notificationService->markRead($request->user(), $notification);

        return response()->json($notification);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $updated = $this->notificationService->markAllRead($request->user());

        return response()->json([
            'updated' => $updated,
            'unread_count' => 0,
        ]);
    }
}
