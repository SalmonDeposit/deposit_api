<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilesTable extends Migration
{
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {            $table->charset = 'utf8';
            $table->uuid('id')->primary();
            $table->string('firstname', 70);
            $table->string('lastname', 70);
            $table->string('email', 150);
            $table->string('phone_number');
            $table->timestamps();

            $table->foreignUuid('user_id')->nullable()->constrained();
            $table->foreignUuid('address_id')->nullable()->constrained();
        });
    }

    public function down()
    {
        Schema::dropIfExists('profiles');
    }
}
