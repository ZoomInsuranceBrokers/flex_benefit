<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMapUserdepTpaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('map_userdep_tpa', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->nullable();        // sfdc ID
            $table->foreignId('user_id_fk')->constrained('users');
            $table->integer('userdep_id_fk')->nullable();
            $table->string('type', 255)->nullable();    // users or dependent
            $table->foreignId('tpa_id_fk')->constrained('tpa');
            $table->string('user_tpa_ext_id', 255)->nullable();    // external id provided by TPA
            $table->text('payload')->nullable();    // extra data json can be saved here
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
        Schema::dropIfExists('map_userdep_tpa');
    }
}
