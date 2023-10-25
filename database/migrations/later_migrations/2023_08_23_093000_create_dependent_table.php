<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDependentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dependent', function (Blueprint $table) {
            $table->id()->from(1000);
            $table->string('sfdc_id')->nullable();        // sfdc ID
            $table->string('dependent_name', 128);
            $table->foreignId('user_id_fk')->constrained('users');
            $table->char('dependent_code',4);  // id from array in env file
            $table->date('dob');
            $table->string('gender',16)->nullable();
            $table->float('nominee_percentage');
            $table->tinyInteger('relationship_type');   // id from array in env file
            $table->tinyInteger('approval_status');   // id from array in env file
            $table->boolean('is_active')->default(true);
            $table->boolean('is_deceased')->default(false);
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
        Schema::dropIfExists('dependent');
    }
}
