<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTpaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tpa', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->nullable();        // sfdc ID
            $table->string('name', 255)->nullable();
            $table->string('username', 255)->nullable();
            $table->string('password', 512)->nullable();    // to be SHA-encoded
            $table->string('port', 8)->nullable();
            $table->text('url')->nullable();
            $table->string('ip_address', 32)->nullable();
            $table->string('domain', 255)->nullable();
            $table->text('payload')->nullable();    // extra data json can be saved here with placeholders
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
        Schema::dropIfExists('tpa');
    }
}
