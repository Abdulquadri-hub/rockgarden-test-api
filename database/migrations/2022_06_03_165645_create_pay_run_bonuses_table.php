<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayRunBonusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pay_run_bonuses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pay_run_id')->nullable(false);
            $table->unsignedBigInteger('bonus_id')->nullable(false);
            $table->unique(['pay_run_id', 'bonus_id'], 'pay_run_bonuses_ids');
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
        Schema::dropIfExists('pay_run_bonuses');
    }
}
