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
    $user = \App\User::first();

    for ($i = 1; $i <= 30; $i++) {
        echo "Permissão: " . $i . ": ";
        if ($user->hasPermission('permission.' . $i, 1)) {
            echo "[ok]<br><br>";
        } else {
            echo "<br><br>";
        }
    }
});
