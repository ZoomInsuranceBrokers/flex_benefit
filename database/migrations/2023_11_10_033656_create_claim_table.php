<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClaimTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('claim', function (Blueprint $table) {
            $table->id()->from(1000);
            // $table->string('external_id')->nullable();        // external system ID
            // $table->string('map_user_fypolicy_id_fk', 128)->nullable();
            // $table->string('map_description', 255)->nullable();
            // $table->foreignId('fypolicy_id_fk')->constrained('map_financial_year_policy');
            // $table->foreignId('user_id_fk')->constrained('users');            
            // $table->integer('status')->nullable()->default(0);
            // $table->boolean('is_active')->default(true);
            // $table->timestamps();
            // $table->string('created_by')->nullable();
            // $table->string('modified_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('claim');
    }
}
