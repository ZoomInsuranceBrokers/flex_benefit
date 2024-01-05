<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePolicyMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('policy_master', function (Blueprint $table) {
            $table->id();
            $table->integer('interval_period')->nullable();
            $table->integer('creation_status');
            $table->string('policy_name', 255)->nullable();
            $table->string('policy_number', 255)->nullable();
            $table->longText('family_definition')->nullable();
            $table->dateTime('policy_start_date');
            $table->dateTime('policy_end_date');
            $table->string('policy_document', 255)->nullable();
            $table->string('policy_status', 255)->nullable();
            $table->integer('tpa_id')->nullable();
            $table->tinyInteger('is_active')->default(1);
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
        Schema::dropIfExists('policy_master');
    }
}
