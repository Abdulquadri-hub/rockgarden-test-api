<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateServiceApplicationClientIdAndPlanStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_application', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('plan_id');
        });

        Schema::table('service_application', function (Blueprint $table) {
            $table->enum('status', ['PENDING', 'DISAPPROVED','APPROVED'])->nullable(false);
            $table->string('plan_name')->nullable(true);
            $table->string('client_last_name')->nullable(true);
            $table->string('client_first_name')->nullable(true);
            $table->string('client_middle_name')->nullable(true);
            $table->string('client_gender')->nullable(true);
            $table->date('client_date_of_birth')->nullable(true);
            $table->string('client_home_address')->nullable(true);
            $table->string('client_office_address')->nullable(true);
            $table->string('client_phone_number')->nullable(true);
            $table->string('client_email')->nullable(true);
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
