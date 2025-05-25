<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameIncidentMediaFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //

        Schema::table('incidents', function (Blueprint $table) {
            $table->renameColumn('image1', 'media1');
            $table->renameColumn('image2', 'media2');
            $table->renameColumn('image3', 'media3');
            $table->renameColumn('image4', 'media4');
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
