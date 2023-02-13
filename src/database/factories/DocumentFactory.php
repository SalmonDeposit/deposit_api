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
        $types = [
            'image',
            'zip',
            'doc',
            'video',
            'unknown'
        ];

        $extensions = [
            'jpeg',
            'jpg',
            'png',
            'zip',
            'xlsx',
            'csv',
            'doc',
            'mp4',
            'mpeg4',
            'mp3',
            'avi',
            'mov',
            'pdf'
        ];

        return [
            'id' => $this->faker->unique()->uuid,
            'user_id' => User::all()->random(1)->get(0)->id,
            'name' => $this->faker->slug(3) . '.' . $extensions[$this->faker->numberBetween(0, sizeof($extensions) - 1)],
            'type' => $types[$this->faker->numberBetween(0, sizeof($types) - 1)],
            'storage_link' => $this->faker->url,
            'size' => $this->faker->numberBetween(1, 4096254),
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }
}
