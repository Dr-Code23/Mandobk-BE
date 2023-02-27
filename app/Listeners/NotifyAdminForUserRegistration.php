<?php

namespace App\Listeners;

use App\Models\User;
use App\Notifications\RegisterUserNotification;
use App\Traits\RoleTrait;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class NotifyAdminForUserRegistration
{
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

        $admins = User::whereIn('role_id', $this->getRolesIdsByName(['ceo']))
            ->get();

        Notification::send($admins, new RegisterUserNotification($event->user));
        // foreach ($admins as $admin) {
        //     $admin->notify(new RegisterUserNotification($event->user));
        // }

        // Make Instance of Notifications Class
        // $registerNotifications = new RegisterUserNotification($event->user);
    }
}
