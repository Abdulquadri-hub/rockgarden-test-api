<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFamilyFriendsAssignmentConstraintStaffAssgment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('family_friend_assignments', function (Blueprint $table) {
            $table->unique(['familyfriend_id', 'client_id']);
        });

        Schema::table('staff_assignments', function (Blueprint $table) {
            $table->unique(['client_id', 'staff_id']);
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
