<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaffReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->string('staff_name');
            $table->string('employee_no');
            $table->string('department');
            $table->string('designation');
            $table->date('report_start_date');
            $table->date('report_end_date');
            $table->integer('total_attendance_days');
            $table->integer('total_working_days');
            $table->decimal('attendance_percentage', 5, 2);
            $table->integer('total_incidents_reported');
            $table->integer('total_staff_charts_created');
            $table->decimal('average_rating', 3, 2)->nullable();
            $table->integer('total_ratings_received');
            $table->json('attendance_details')->nullable();
            $table->json('incident_details')->nullable();
            $table->json('staff_chart_details')->nullable();
            $table->json('payrun_details')->nullable();
            $table->text('summary_notes')->nullable();
            $table->string('pdf_path')->nullable();
            $table->enum('status', ['generated', 'downloaded', 'archived'])->default('generated');
            $table->timestamps();

            $table->foreign('staff_id')->references('id')->on('employees')->onDelete('cascade');
            $table->index(['staff_id', 'report_start_date', 'report_end_date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('staff_reports');
    }
}
