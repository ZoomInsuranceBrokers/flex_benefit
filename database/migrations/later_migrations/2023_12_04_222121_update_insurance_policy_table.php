<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateInsurancePolicyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('insurance_policy', function (Blueprint $table) {
            $table->boolean('is_grade_based')->default(false)->after('is_base_plan');
            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('insurance_policy', function (Blueprint $table) {
            $table->dropColumn('is_grade_based');
        });
    }
}
