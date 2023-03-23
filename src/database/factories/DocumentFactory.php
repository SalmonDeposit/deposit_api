<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Document;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentFactory extends Factory
{
    protected $model = Document::class;

    public function definition(): array
    {
        $types = [
            'application/pdf',
            'application/pdf',
            'application/pdf',
            'application/pdf',
            'image/jpeg',
            'image/jpeg',
            'image/jpeg',
            'image/jpeg',
            'image/png',
            'image/png',
            'image/png',
            'image/png',
            'video/mp4',
            'video/mpeg',
            'text/csv',
            'text/csv',
            'text/csv',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ];

        $mimeType = $this->faker->randomElement($types);
        $size = $this->predictSize($mimeType ?? null);

        return [
            'id' => $this->faker->unique()->uuid,
            'name' => $this->faker->slug(3) . '.example',
            'type' => $types[$this->faker->numberBetween(0, sizeof($types) - 1)],
            'storage_link' => $this->faker->url . '/document.example',
            'size' => $size,
        ];
    }

    /**
     * @param $mimeType
     * @return int
     */
    private function predictSize($mimeType): int
    {
        $size = 1;

        switch ($mimeType) {
            case 'application/pdf':
                $size = $this->faker->numberBetween(150, 15000000);
                break;
            case 'image/jpeg':
                $size = $this->faker->numberBetween(1150000, 30000000);
                break;
            case 'image/png':
                $size = $this->faker->numberBetween(150000, 4500000);
                break;
            case 'video/mpeg':
            case 'video/mp4':
                $size = $this->faker->numberBetween(50000000, 750000000);
                break;
            case 'text/csv':
            case 'application/vnd.openxmlformats-officedocument.presentationml.presentation':
            case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                $size = $this->faker->numberBetween(6000000, 35000000);
                break;
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                $size = $this->faker->numberBetween(1, 25000000);
                break;
        }

        return $size;
    }
}
