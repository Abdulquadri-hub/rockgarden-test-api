<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_groups', function (Blueprint $table) {
            $table->id();
            $table->string('group_name')->nullable(false);
            $table->boolean('taxable')->nullable(false);
            $table->string('type')->nullable(true);
            $table->text('attributes')->nullable(true);
            $table->string('unit')->nullable(false);
            $table->json('images')->nullable(true);
            $table->boolean('returnable')->nullable(false);
            $table->string('dimension')->nullable(true);
            $table->float('weight_kg')->nullable(false);
            $table->string('manufacturer')->nullable(true);
            $table->string('brand')->nullable(true);
            $table->float('cost_price')->nullable(false);
            $table->float('sale_price')->nullable(false);
            $table->string('currency')->nullable(true);
            $table->text('description')->nullable(true);
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
        Schema::dropIfExists('item_groups');
    }
}
