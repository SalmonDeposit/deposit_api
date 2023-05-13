<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->uuid('id');
            $table->string('short_name');
            $table->string('long_name')->nullable();
            $table->integer('salmon_coin_cost');

            $table->timestamps();
        });

        DB::table('plans')->insert([
            ['id' => Ramsey\Uuid\Uuid::uuid4(), 'short_name' => 'Basic', 'salmon_coin_cost' => 100],
            ['id' => Ramsey\Uuid\Uuid::uuid4(), 'short_name' => 'Standard', 'salmon_coin_cost' => 500],
            ['id' => Ramsey\Uuid\Uuid::uuid4(), 'short_name' => 'Premium', 'salmon_coin_cost' => 1000]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plans');
    }
}
