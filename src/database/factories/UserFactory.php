<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        $now = now();
        return [
            'id' => $this->faker->unique()->uuid,
            'email' => $this->faker->unique()->safeEmail,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'simon_coin_stock' => $this->faker->numberBetween(0, 255),
            'email_verified_at' => $now,
            'remember_token' => Str::random(10),
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }
}
