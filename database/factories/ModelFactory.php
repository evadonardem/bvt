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
        'name' => $faker->name,
        'email' => $faker->email,
        'password' => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Price::class, function(Faker\Generator $faker) {
	return [
		'datetime_posted' => $faker->date($format = 'Y-m-d', $max = 'now') . ' ' . $faker->time($format = 'H-i-s', $max = 'now'),
		'unit_price' => $faker->randomFloat(2, 10, 1000)
	];
});