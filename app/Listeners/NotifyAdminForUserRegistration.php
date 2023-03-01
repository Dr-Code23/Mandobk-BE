<?php

namespace App\Listeners;

use App\Models\User;
use App\Notifications\RegisterUserNotification;
use App\Traits\RoleTrait;
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
    public function handle($event): void
    {

        $admins = User::whereIn(
            'role_id',
            $this->getRolesIdsByName(
                [
                    'ceo'
                ]
            )
        )
            ->get(['id']);

        Notification::send($admins, new RegisterUserNotification($event->user));
    }
}
