<?php

namespace App\Listeners;

use App\Models\User;
use App\Notifications\OrderStatusNotification;
use App\Traits\RoleTrait;
use App\Traits\UserTrait;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class TellCustomersAboutOrderStatus
{
    use UserTrait;
    use RoleTrait;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $users = User::whereIn('id', $this->getSubUsersForUser($event->userId))->get(['id']);
        info($users);
        Notification::send(
            $users,
            new OrderStatusNotification($event->order)
        );
    }
}
