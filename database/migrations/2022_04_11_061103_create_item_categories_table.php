<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_categories', function (Blueprint $table) {
            $table->id();
            $table->string("name")->unique()->nullable(false);
            $table->text("description")->nullable(true);
            $table->timestamps();
        });

        Schema::table('items', function (Blueprint $table) {
            $table->string('name')->nullable(true)->change();
            $table->string('category_name')->nullable(true);
            $table->float('cost_price')->nullable()->change();
            $table->float('sale_price')->nullable()->change();
            $table->boolean('returnable')->nullable()->change();
            $table->float('weight_kg')->nullable()->change();
            $table->dropColumn('images');
            $table->string('image1')->nullable();
            $table->string('image2')->nullable();
            $table->string('image3')->nullable();
        });

        Schema::table('stocks', function (Blueprint $table) {
            $table->unsignedInteger('reorder_level')->nullable(true);
        });

        Schema::table('vendors', function (Blueprint $table) {
            $table->string('contact_person')->nullable(true);
            $table->string('first_name')->nullable(true)->change();
            $table->string('last_name')->nullable(true)->change();
            $table->string('vendor_address')->nullable(true);
            $table->string('civility')->nullable(true)->change();
            $table->string('vendor_no')->nullable(true)->change();
        });

        Schema::table('sale_orders', function (Blueprint $table) {
            $table->unsignedInteger('client_id')->nullable(true)->change();
            $table->renameColumn('staff_id', 'created_by_staff_id');
            $table->renameColumn('total', 'total_amount');
            $table->float('shipping_charges')->nullable(true)->change();
            $table->date('order_date')->nullable(true)->change();
            $table->string('order_no')->nullable(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_categories');
    }
}
