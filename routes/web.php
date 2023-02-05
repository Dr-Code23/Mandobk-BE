<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/link', function () {
    Artisan::call('storage:link');

    return 'done';
});
Route::get('/migrate', function () {
    Artisan::call('migrate:fresh --seed');

    return 'migration';
});

Route::get('/seed', function () {
    Artisan::call('db:seed');

    return;
});

Route::get('/link', function () {
    Artisan::call('storage:link');

    return view('welcome');
});

// Clear config cache:
Route::get('/config-cache', function () {
    Artisan::call('config:cache');

    return view('welcome');
});
Route::get('/config-cache', function () {
    Artisan::call('cache:clear');

    return view('welcome');
});
