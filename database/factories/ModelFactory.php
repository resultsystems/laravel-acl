<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
 */

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'name'           => $faker->name,
        'email'          => $faker->email,
        'password'       => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
    ];
});

$factory->define(ResultSystems\Acl\Branch::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
    ];
});

$factory->define(ResultSystems\Acl\Permission::class, function (Faker\Generator $faker) {
    $name = $faker->name;

    return [
        'name'     => $name,
        'slug'     => str_slug($name),
        'comments' => $faker->sentence,
    ];
});

$factory->define(ResultSystems\Acl\Role::class, function (Faker\Generator $faker) {
    $name = $faker->name;

    return [
        'name' => $name,
        'slug' => str_slug($name),
    ];
});
