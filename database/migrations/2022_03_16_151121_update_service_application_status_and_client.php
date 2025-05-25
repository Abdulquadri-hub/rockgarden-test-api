<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateServiceApplicationStatusAndClient extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('room_location')->nullable(true);
            $table->string('room_number')->nullable(true);
            $table->string('room_suffix')->nullable(true);
            $table->string('prev_address')->nullable(true);
            $table->string('postal_code')->nullable(true);
            $table->string('admitted_from')->nullable(true);
            $table->string('admitted_by')->nullable(true);

        });

        Schema::table('service_application', function (Blueprint $table) {
            $table->unsignedInteger('status');
        });

        Schema::table('staff_charts', function (Blueprint $table) {
            $table->boolean('is_red_flag')->nullable(false);
            $table->string('media1')->nullable();
            $table->string('media2')->nullable();
            $table->string('media3')->nullable();
            $table->string('media4')->nullable();
            $table->renameColumn('record_time', 'report_time');
            $table->renameColumn('record_date', 'report_date');
            $table->text('category')->nullable();
            $table->string('health_issue')->nullable();
            $table->string('cream_applied')->nullable();
            $table->text('consequence_event')->nullable();
            $table->text('location')->nullable();
            $table->text('reason_for_visit')->nullable();
            $table->text('resultant_action')->nullable();
            $table->boolean('is_age6069')->nullable();
            $table->boolean('is_age7079')->nullable();
            $table->boolean('is_age8089')->nullable();
            $table->boolean('is_age90_plus')->nullable();
            $table->text('movement_going_up_stairs')->nullable();
            $table->text('movement_coming_down_stairs')->nullable();
            $table->text('suggested_equip_resident')->nullable();
            $table->text('suggested_equip_staff')->nullable();
            $table->text('any_safeguarding_issues')->nullable();
            $table->text('risk_to_children')->nullable();
            $table->text('risk_to_offending')->nullable();
            $table->text('any_other_areas_of_risk')->nullable();
            $table->string('dosage_afternoon_when')->nullable();
            $table->string('dosage_evening')->nullable();
            $table->string('dosage_evening_when')->nullable();
            $table->string('dosage_afternoon')->nullable();
            $table->text('reason_for_medication')->nullable();
            $table->text('other_intake_guide')->nullable();
            $table->boolean('is_morning_dose_administered')->nullable();
            $table->boolean('is_afternoon_dose_administered')->nullable();
            $table->string('is_evening_dose_administered')->nullable();
            $table->date('start_date')->nullable();
            $table->string('is_positive_covid_19')->nullable();
            $table->float('weight_pounds')->nullable();
            $table->boolean('use_kilogram')->nullable();
            $table->text('description')->nullable();
            $table->text('result')->nullable();
            $table->float('score')->nullable();
            $table->string('drink_type')->nullable();
            $table->text('delivery_method')->nullable();
            $table->string('quantity_ml')->nullable();
            $table->text('level_of_need')->nullable();
            $table->text('present_situation')->nullable();
            $table->text('actions')->nullable();
            $table->string('family_participant')->nullable();
            $table->string('other_participants')->nullable();
            $table->string('resident_needs')->nullable();
            $table->float('weight_kg')->nullable();
            $table->date('review_date')->nullable();
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
