<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTpaMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tpa_master', function (Blueprint $table) {
            $table->id();
            $table->string('tpa_company_name', 50);
            $table->string('address', 50);
            $table->string('state_name', 50);
            $table->string('city_name', 50);
            $table->string('pincode', 50);
            $table->tinyInteger('status')->default(1);
            $table->string('tpa_comp_icon_url', 1000)->nullable();
            $table->string('tpa_table_name', 255)->nullable();
            $table->timestamps(); // created_at and updated_at columns
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tpa_master');
    }
}
