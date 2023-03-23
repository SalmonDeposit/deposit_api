<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Document;
use App\Models\Offer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $years = ['2022', '2023', '2024'];
        $hype_multiplicator = 2;

        // Foreach years ...
        for ($y = 0; $y < sizeof($years); $y++) {
            $year = $years[$y];

            // Foreach month of this year ...
            for ($m = 1; $m <= 12; $m++) {
                $month_text = $m < 10 ? '0'.$m : $m;
                $day_text = rand(1, 26);
                $day_text = $day_text < 10 ? '0'.$day_text : $day_text;

                $date = Carbon::parse("$year-$month_text-$day_text");
                $users_to_create = $y === 0 ? 500 : 500 * $y * $hype_multiplicator;
                User::factory(round($users_to_create / 12))->create([
                    'created_at' => $date,
                    'updated_at' => $date
                ])->each(function($user) use ($date) {
                    $random_offer = rand(1, 1000);

                    if ($random_offer <= 4) {
                        $offer = 'Premium';
                        $documents_range = [500, 2000];
                    } elseif ($random_offer <= 24) {
                        $offer = 'Standard';
                        $documents_range = [250, 500];
                    } elseif ($random_offer <= 150) {
                        $offer = 'Basic';
                        $documents_range = [50, 200];
                    } else {
                        $offer = 'Freemium';
                        $documents_range = [5, 20];
                    }

                    $offerModel = Offer::where([
                        'wording' => $offer
                    ])->first();

                    $offerModel->users()->save($user);

                    $documents = Document::factory(
                        rand($documents_range[0], $documents_range[1])
                    )->create([
                        'created_at' => $date,
                        'updated_at' => $date
                    ]);

                    $user->documents()->saveMany($documents);
                });

                Document::inRandomOrder()->limit(round(Document::count('id') / 100 * 5))->delete();
            }
        }
    }
}
