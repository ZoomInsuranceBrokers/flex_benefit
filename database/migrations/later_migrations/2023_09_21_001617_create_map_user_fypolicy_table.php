<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMapUserFypolicyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('map_user_fypolicy', function (Blueprint $table) {
            $table->id()->from(1000);
            $table->string('external_id')->nullable();        // external system ID
            $table->string('map_name', 128)->nullable();
            $table->string('map_description', 255)->nullable();
            $table->foreignId('fypolicy_id_fk')->constrained('map_financial_year_policy');
            $table->foreignId('user_id_fk')->constrained('users');            
            $table->integer('points_used')->nullable()->default(0);
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
        Schema::dropIfExists('map_user_fypolicy');
    }
}
