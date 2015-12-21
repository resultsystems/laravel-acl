<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
 */

Route::get('/', function () {
    $user = \App\User::with(['branches.roles' => function ($query) {
        $query->where("id", "=", 1);
    }])->first();
    echo "<pre>";
    print_r($user->toArray());
    echo "</pre>";
    exit;

    return $user;
});
