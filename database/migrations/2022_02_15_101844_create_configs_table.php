<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configs', function (Blueprint $table) {
            $table->id();
            $table->string('app_name')->nullable();
            $table->string('app_name_abv')->nullable();
            $table->string('app_slogan')->nullable();
            $table->string('captcha')->nullable();
            $table->string('datasitekey')->nullable();
            $table->string('recaptcha_secret')->nullable();
            $table->string('img_login')->nullable();
            $table->string('caminho_img_login')->nullable();
            $table->integer('tamanho_img_login')->nullable();
            $table->string('titulo_login')->nullable();
            $table->string('layout')->nullable();
            $table->string('skin')->nullable();
            $table->string('favicon')->nullable();
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
        Schema::dropIfExists('configs');
    }
}
