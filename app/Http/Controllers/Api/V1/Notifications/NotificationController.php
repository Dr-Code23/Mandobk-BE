<?php

namespace App\Http\Controllers\Api\V1\Notifications;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Notifications\NotificationCollection;
use App\Http\Resources\Api\V1\Notifications\NotificationResource;
use App\Models\User;
use App\Notifications\RegisterUserNotification;
use App\Services\Api\V1\Notifications\NotificationService;
use App\Traits\DateTrait;
use App\Traits\HttpResponse;
use App\Traits\Translatable;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Collection;

class NotificationController extends Controller
{
    use DateTrait;
    use HttpResponse;
    use Translatable;
    public function __construct(
        private NotificationService $notificationService
    ) {
    }
    public function index(Request $request)
    {
        $notifications = $this->notificationService->index($request);
        return $this->resourceResponse(new NotificationCollection($notifications));
    }

    public function show(DatabaseNotification $notification)
    {
        return $this->resourceResponse(new NotificationResource($notification));
    }

    public function markAsRead(DatabaseNotification $notification)
    {
        if (!$notification->read_at) {
            auth()->user()->notifications()->where('id', $notification->id)->update(['read_at' => now()]);
        }
        return $this->resourceResponse(null, 'Notification Marked Successfully');
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications()->where('read_at', null)->update(['read_at' => now()]);
        return $this->resourceResponse(null, 'Notifications Marked Successfully');
    }
    public function destroy(DatabaseNotification $notification)
    {
        $notification->delete();
        return $this->success(null, 'Notification Deleted successfully');
    }

    public function destroyAll()
    {
        auth()->user()->notifications()->delete();
        return $this->success(null, 'Notifications Deleted Successfully');
    }
}
