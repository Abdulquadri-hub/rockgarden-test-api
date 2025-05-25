<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('civility')->nullable(false);
            $table->string('first_name')->nullable(true);
            $table->string('last_name')->nullable(false);
            $table->string('company_name')->nullable(true);
            $table->string('vendor_email')->nullable(true);
            $table->string('vendor_phone')->nullable(true);
            $table->string('vendor_web_site')->nullable(true);
            $table->text('remarks')->nullable(true);
            $table->string('vendor_no')->nullable(false);
            $table->string('primary_contacts');
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
        Schema::dropIfExists('vendors');
    }
}
