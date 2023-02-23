<?php

namespace App\Listeners;

use App\Models\User;
use App\Notifications\RegisterUserNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class NotifyAdminForUserRegistration
{
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
        $admins = User::whereHas('role', function ($query) {
            $query->whereIn('name', [
                'ceo',
                'monitor_and_evaluation',
            ]);
        })->get();

        info($event->user);
        // Notification::send()
        Notification::send(
            $admins,
            new RegisterUserNotification($event->user)
        );
    }
}
