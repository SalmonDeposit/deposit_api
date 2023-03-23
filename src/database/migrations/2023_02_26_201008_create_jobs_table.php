<?php

declare(strict_types=1);

use App\Console\Commands\DocumentsTypesThreeYearsExtract;
use App\Console\Commands\OffersUsage;
use App\Console\Commands\UsersDocumentsStatsExtract;
use App\Console\Commands\UsersExtract;
use App\Models\Job;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobsTable extends Migration
{
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('class')->unique();
            $table->boolean('running')->default(false);
            $table->string('status', 50)->default('Available');
            $table->text('message')->nullable();

            $table->dateTime('launched_at')->nullable();
            $table->dateTime('finished_at')->nullable();
        });

        Job::create([
            'class' => UsersExtract::class,
            'running' => 0,
            'status' => 'AVAILABLE'
        ]);

        Job::create([
            'class' => UsersDocumentsStatsExtract::class,
            'running' => 0,
            'status' => 'AVAILABLE'
        ]);

        Job::create([
            'class' => DocumentsTypesThreeYearsExtract::class,
            'running' => 0,
            'status' => 'AVAILABLE'
        ]);

        Job::create([
            'class' => OffersUsage::class,
            'running' => 0,
            'status' => 'AVAILABLE'
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('documents');
    }
}
