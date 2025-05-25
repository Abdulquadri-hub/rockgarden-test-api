<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateStaffChartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('staff_charts', function (Blueprint $table) {
            $table->boolean('is_full_mobile')->nullable();
            $table->boolean('use_walking_aid')->nullable();
            $table->boolean('is_wheel_chair_dependant')->nullable();
            $table->boolean('need_assistance_of_one')->nullable();
            $table->boolean('need_assistance_of_two')->nullable();
            $table->boolean('is_fully_dependant')->nullable();
            $table->boolean('has_demantia_frail')->nullable();
            $table->boolean('has_high_blood_pressure')->nullable();
            $table->boolean('has_poor_circulation')->nullable();
            $table->boolean('has_cva_tia')->nullable();
            $table->boolean('has_osteo_rheumatoid_arthritis')->nullable();
            $table->boolean('has_osteoporosis')->nullable();
            $table->boolean('has_poor_vision')->nullable();
            $table->boolean('has_poor_hearing')->nullable();
            $table->boolean('has_diabetes')->nullable();
            $table->boolean('has_amputee')->nullable();
            $table->boolean('has_history_of_falls')->nullable();
            $table->boolean('admitted_for_investigation')->nullable();
            $table->boolean('has_catheter')->nullable();
            $table->boolean('has_inco_urine')->nullable();
            $table->boolean('has_inco_faeces')->nullable();
            $table->boolean('has_incontinent_doubly')->nullable();
            $table->boolean('requires_assistance')->nullable();
            $table->boolean('uses_diuretics')->nullable();
            $table->boolean('uses_sedatives')->nullable();
            $table->boolean('uses_tranquilisers')->nullable();
            $table->boolean('uses_anti_hypertensive')->nullable();
            $table->boolean('uses_reg_aperient')->nullable();
            $table->boolean('uses_hypoglycaemic_agents')->nullable();
            $table->boolean('drink_alcohol')->nullable();
            $table->string('resident_height')->nullable();
            $table->string('resident_weight')->nullable();
            $table->string('info_comprehension')->nullable();
            $table->string('info_behavior')->nullable();
            $table->string('info_disability')->nullable();
            $table->string('handling_pain')->nullable();
            $table->string('handling_skin')->nullable();
            $table->string('handling_other')->nullable();
            $table->string('movement_walking')->nullable();
            $table->string('movement_standing')->nullable();
            $table->string('movement_using_toilet')->nullable();
            $table->string('movement_going_to_bed')->nullable();
            $table->string('movement_getting_from_bed')->nullable();
            $table->string('movement_on_bed')->nullable();
            $table->string('visit_type')->nullable();
            $table->string('visit_location')->nullable();
            $table->string('visitor_name')->nullable();
            $table->string('bowel_type')->nullable();
            $table->string('weight_grams')->nullable();
            $table->string('weight_stone')->nullable();
            $table->string('food_period')->nullable();
            $table->string('food_type')->nullable();
            $table->string('review_type')->nullable();
            $table->string('service_user_participant')->nullable();
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
