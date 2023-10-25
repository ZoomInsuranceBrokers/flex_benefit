<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInsuranceSubcategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('insurance_subcategory', function (Blueprint $table) {
            $table->id()->from(1000);
            $table->string('sfdc_id')->nullable();            
            $table->foreignId('ins_category_id_fk')->constrained('insurance_category');
            $table->string('name', 64);
            $table->string('fullname', 128);
            $table->string('description', 100)->nullable();
            $table->longText('details')->nullable();
            $table->boolean('has_core_multiple')->default(true);
            $table->string('core_multiple')->nullable();    // 1X, 2X
           // $table->boolean('has_sum_assured')->default(true);
            //$table->string('sum_assured')->nullable();
            $table->string('created_by')->nullable();
            $table->string('modified_by')->nullable();
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
        Schema::dropIfExists('insurance_subcategory');
    }
}
