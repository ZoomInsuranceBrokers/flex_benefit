<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinancialYearsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('financial_years', function (Blueprint $table) {
            $table->id()->from(1000);
            $table->string('external_id')->nullable();        // sfdc ID
            $table->string('name', 64);
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('future_fy_year_fk')->nullable();    // self join relation
            $table->integer('prev_fy_year_fk')->nullable();    // self join relation           
            $table->boolean('is_active')->default(false);
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
        Schema::dropIfExists('financial_years');
    }
}
