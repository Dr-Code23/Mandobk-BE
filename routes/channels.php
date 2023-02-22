<?php

use App\Events\RegisterUserEvent;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Broadcast::channel('google', function () {
//     return true;
// });

// Register New Visitor

Broadcast::channel(RegisterUserEvent::$channelName, function () {
    return true;
});

// Broadcast::channel('pusher', function ($user) {
//     return auth()->id() == $user->id;
// });


// newUserRegister Channel

Broadcast::channel(RegisterUserEvent::$channelName, function ($user) {
    return in_array($user->role->name, config('notifications.newUserRegister'));
});
