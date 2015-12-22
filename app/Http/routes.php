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
    if ($user->hasPermission(['permission.23', 'permission.24'], false)) {
        echo "ok<br>";
    } else {
        echo "não ok<br>";
    }

    for ($i = 1; $i <= 30; $i++) {
        echo "Permissão: " . $i . ": ";
        if ($user->hasPermission('permission.' . $i, true, 1)) {
            echo "[ok]";
        }
        echo "<br>";
        echo "Permissão: " . $i . ": ";
        if ($user->hasPermission('permission.' . $i)) {
            echo "[ok]";
        }
        echo "<br>";
    }
});
