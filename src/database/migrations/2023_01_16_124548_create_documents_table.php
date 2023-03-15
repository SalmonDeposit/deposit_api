<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentsTable extends Migration
{
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 150);
            $table->string('type');
            $table->text('storage_link');
            $table->integer('size');
            $table->timestamps();

            $table->foreignUuid('user_id')->nullable()->constrained();
        });
    }

    public function down()
    {
        Schema::dropIfExists('documents');
    }
}
