<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInsuranceEndorsementDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('insurance_endorsement_data', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id');
            $table->string('insured_name');
            $table->string('relation');
            $table->date('dob');
            $table->integer('age');
            $table->date('date_of_joining');
            $table->string('gender');
            $table->string('tpa_id');
            $table->integer('sum_insured');
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
        Schema::dropIfExists('insurance_endorsement_data');
    }
}
