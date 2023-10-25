<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMapFinancialYearPolicyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('map_financial_year_policy', function (Blueprint $table) {
            $table->id()->from(1000);
            $table->string('sfdc_id')->nullable();        // sfdc ID
            $table->string('map_name', 128)->nullable();
            $table->string('map_description', 255)->nullable();
            $table->foreignId('fy_id_fk')->constrained('financial_years');
            $table->foreignId('ins_policy_id_fk')->constrained('insurance_policy');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->string('created_by')->nullable();
            $table->string('modified_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('policy_cluster');
    }
}
