<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $faker = \Faker\Factory::create('fr_FR');

        return [
            'id' => $faker->unique()->uuid,
            'line' => $faker->streetAddress,
            'postal_code' => Str::replace(' ', '', $faker->postcode),
            'city' => $faker->city,
            'country' => $faker->country,
            'is_default' => true
        ];
    }
}
