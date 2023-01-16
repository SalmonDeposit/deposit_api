<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Document;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory(50)->create();
        Profile::factory(150)->create();
        Document::factory(1000)->create();
    }
}
