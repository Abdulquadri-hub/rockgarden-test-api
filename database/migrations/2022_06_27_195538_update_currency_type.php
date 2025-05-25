<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCurrencyType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bonuses', function (Blueprint $table) {
            $table->string( 'currency')->nullable()->change();
        });

        Schema::table('allowances', function (Blueprint $table) {
            $table->string( 'currency')->nullable()->change();
        });

        Schema::table('deductions', function (Blueprint $table) {
            $table->string( 'currency')->nullable()->change();
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
