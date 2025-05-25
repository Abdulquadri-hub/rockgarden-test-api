<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateStaffAssignmentFamilyFriends extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('family_friend_assignments', function ($table) {
            $table->dropColumn('service_application_id');
        });

        Schema::table('staff_assignments', function ($table) {
            $table->dropColumn('service_application_id');
        });

        Schema::table('family_friend_assignments', function (Blueprint $table) {
            $table->unsignedInteger('client_id')->nullable(true);
        });

        Schema::table('staff_assignments', function (Blueprint $table) {
            $table->unsignedInteger('client_id')->nullable(true);
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
