<?php

use Faker\Generator as Faker;

$factory->define(App\Models\ProductDetail::class, function (Faker $faker) {
    return [
        'imgs' => [$faker->imageUrl(), $faker->imageUrl(), $faker->imageUrl()],
        'spec' => $faker->sentence,
    ];
});
