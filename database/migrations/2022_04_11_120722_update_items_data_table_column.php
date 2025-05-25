<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateItemsDataTableColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->string('sku')->unique()->nullable(true);
            $table->unsignedInteger('reorder_level')->nullable(true);
            $table->unsignedInteger('vendor_id')->nullable();
        });

        Schema::table('stocks', function (Blueprint $table) {
            $table->dropColumn('reorder_level');
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
