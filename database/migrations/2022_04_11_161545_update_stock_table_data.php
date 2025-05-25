<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateStockTableData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->string('item_name')->nullable(true);
            $table->string('item_category')->nullable(true);
            $table->integer('stock_level_before')->nullable(true);
            $table->integer('stock_level_after')->nullable(true);
            $table->float('stock_entry')->nullable(true);
            $table->unsignedInteger('created_by_user_id')->nullable(true);
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
