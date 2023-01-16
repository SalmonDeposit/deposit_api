<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    protected $model = Profile::class;

    public function definition(): array
    {
        $now = now();
        return [
            'id' => $this->faker->unique()->uuid,
            'user_id' => User::all()->random(1)->get(0)->id,
            'firstname' => $this->faker->firstName,
            'lastname' => $this->faker->lastName,
            'email' => $this->faker->email,
            'phone_number' => $this->faker->e164PhoneNumber,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }
}
