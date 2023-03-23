<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('wording', 50)->unique();
            $table->integer('max_available_space');
            $table->decimal('price_excluding_tax', 5);
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignUuid('offer_id')->nullable()->constrained();
        });

        $offers = [
            ['Freemium', 1, 0.00], ['Basic', 10, 3.99], ['Standard', 50, 14.99], ['Premium', 100, 29.99]
        ];

        foreach ($offers as $offer) {
            \App\Models\Offer::create([
                'wording' => $offer[0],
                'max_available_space' => $offer[1],
                'price_excluding_tax' => $offer[2]
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('offers');
    }
}
