<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Document;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory(50)->create()->each(function($user) {
            Profile::factory(rand(1, 3))->create()->each(function($profile) use ($user) {
                $user->profiles()->save($profile);
                if (rand(0, 1) === 1) {
                    $address = Address::factory()->create();
                    $profile->address()->associate($address->id)->save();
                }
            });

            Document::factory(rand(5, 20))->create()->each(function($document) use ($user) {
                $user->documents()->save($document);
            });
        });
    }
}
