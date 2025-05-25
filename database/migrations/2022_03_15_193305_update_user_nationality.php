<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUserNationality extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function ($table) {
            $table->dropColumn('nationality');
            $table->dropColumn('sex_of_carer_pref');
        });

        Schema::table('employees', function ($table) {
            $table->dropColumn('national_identification_number');
            $table->dropColumn('nationality');
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->string('nationality')->nullable(true);
            $table->enum('sex_of_carer_pref', ['Male', 'Female', 'Both', 'Other'])->nullable(true);
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->string('national_identification_number')->nullable(true);
            $table->string('nationality')->nullable(true);
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
