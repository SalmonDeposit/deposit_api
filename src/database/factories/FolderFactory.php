<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Document;
use App\Models\Folder;
use Illuminate\Database\Eloquent\Factories\Factory;

class FolderFactory extends Factory
{
    protected $model = Folder::class;

    public function definition(): array
    {
        $now = now();

        return [
            'id' => $this->faker->unique()->uuid,
            'name' => $this->faker->word,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }
}
