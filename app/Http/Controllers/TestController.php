<?php

namespace App\Http\Controllers;

use App\Events\PusherEvent;
use App\Events\TestEvent;
use App\Models\User;
use App\Notifications\TestNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class TestController extends Controller
{
    public function index()
    {

        // TestEvent::dispatch(User::find(1));
        PusherEvent::dispatch('Hello World');
        return 'Nice';
    }
}
