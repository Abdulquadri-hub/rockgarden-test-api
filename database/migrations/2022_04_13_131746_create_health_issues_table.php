<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHealthIssuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('health_issues', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->string('review_frequency')->nullable();
            $table->string('initial_treatment_plan')->nullable();
            $table->string('closed_reason')->nullable();
            $table->unsignedInteger('recorded_by_staff_id')->nullable();
            $table->unsignedInteger('closed_by_user_id')->nullable();
            $table->unsignedInteger('client_id')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('closed_date')->nullable();
            $table->string('image1')->nullable();
            $table->string('image2')->nullable();
            $table->string('image3')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('health_issues');
    }
}
