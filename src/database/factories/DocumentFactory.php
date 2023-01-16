<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

class DocumentFactory extends Factory
{
    protected $model = Document::class;

    public function definition(): array
    {
        $now = now();
        return [
            'id' => $this->faker->unique()->uuid,
            'user_id' => User::all()->random(1)->get(0)->id,
            'name' => Str::slug($this->faker->text(150)),
            'storage_link' => $this->faker->url,
            'size' => $this->faker->numberBetween(1, 4096254),
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }
}
