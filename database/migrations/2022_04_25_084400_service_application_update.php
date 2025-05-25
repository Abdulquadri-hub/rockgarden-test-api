<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ServiceApplicationUpdate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_application', function (Blueprint $table) {
            $table->string('client_state')->nullable();
            $table->string('client_city')->nullable();
            $table->string('disapproval_reason')->nullable();
            $table->boolean('applying_for_self')->nullable();
            $table->string('relation_next_of_kin')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
