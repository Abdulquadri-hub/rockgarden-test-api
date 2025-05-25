<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateRotaField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rotas', function ($table) {
            $table->dropUnique('rotas_rota_date_shift_ids_staff_ids_unique');
            $table->dropColumn('shift_ids');
            $table->dropColumn('rota_date');
            $table->dropColumn('staff_ids');
        });

        Schema::table('rotas', function (Blueprint $table) {
            $table->unsignedInteger('staff_id')->nullable(false);
            $table->string('shift')->nullable(false);
            $table->date('working_date')->nullable(false);
            $table->unique(['staff_id', 'shift', 'working_date']);
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
