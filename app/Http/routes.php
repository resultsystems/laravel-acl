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
    //dd($collection->every(4, 1));
    $user = \App\User::where('id', 1)->first();
    $p1 = 'permission.2';
    $p2 = 'permission.7';
    if ($user->hasPermission($p1)) {
        echo 'p1 ok<br>';
    } else {
        echo 'sem p1<br>';
    }
    if ($user->hasPermission($p2)) {
        echo 'p2 ok<br>';
    } else {
        echo 'sem p2<br>';
    }

    if ($user->hasPermission([$p1, $p2], false)) {
        echo "p1 e p2 ok any=false<br>";
    } else {
        echo "sem permissão p1 e p2 any=false<br>";
    }
    if ($user->hasPermission([$p1, $p2], true)) {
        echo "p1 e p2 ok any=true<br>";
    } else {
        echo "sem permissão p1 e p2 any=true<br>";
    }
    echo '<br>';

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
