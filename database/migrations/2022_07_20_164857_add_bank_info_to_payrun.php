<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBankInfoToPayrun extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pay_runs', function (Blueprint $table) {
            $table->string( 'bank_name')->nullable();
            $table->string( 'bank_account_number')->nullable();
            $table->string( 'department')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payrun', function (Blueprint $table) {
            //
        });
    }
}
