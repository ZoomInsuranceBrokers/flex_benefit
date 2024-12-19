<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMapGradeCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('map_grade_category', function (Blueprint $table) {
            $table->id()->from(1);
            $table->string('external_id')->nullable();        // external system ID
            $table->foreignId('grade_id_fk')->constrained('grade');
            $table->foreignId('category_id_fk')->constrained('insurance_category');      
            $table->float('amount',12,2)->nullable();
            $table->boolean('is_active')->default(true);            
            $table->timestamps();
            //$table->string('created_by')->nullable();
            //$table->string('modified_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('map_grade_category');
    }
}
