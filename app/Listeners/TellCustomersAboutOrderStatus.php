<?php

namespace App\Listeners;

use App\Models\User;
use App\Notifications\OrderStatusNotification;
use App\Traits\RoleTrait;
use App\Traits\UserTrait;
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
     */
    public function handle($event): void
    {
        $users = User::whereIn(
            'id',
            $this->getSubUsersForUser($event->userId)
        )->get(['id']);

        Notification::send(
            $users,
            new OrderStatusNotification($event->order)
        );
    }
}
