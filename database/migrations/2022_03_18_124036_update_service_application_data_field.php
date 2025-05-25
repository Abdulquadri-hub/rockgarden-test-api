<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateServiceApplicationDataField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('staff_charts', function (Blueprint $table) {
            $table->dropColumn('report_date');
            $table->dropColumn('report_time');
        });

        Schema::table('staff_charts', function (Blueprint $table) {
            $table->string('report_date')->nullable();
            $table->string('report_time')->nullable();
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
