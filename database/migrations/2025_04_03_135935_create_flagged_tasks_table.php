<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFlaggedTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flagged_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id');
            $table->string('task_type'); // 'charting', 'attendance', etc.
            $table->string('flag_color')->default('yellow');
            $table->string('status')->default('pending');
            $table->text('description');
            $table->timestamps();
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('flagged_tasks');
    }
}

