<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactPoliciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contact_policies', function (Blueprint $table) {
            // $table->id()->from(100);
            // $table->string('sfdc_id')->unique();        // sfdc ID
            // $table->string('dependent_name', 128);
            // $table->foreignId('contact_id_fk')->constrained('contacts');
            // $table->tinyInteger('dependent_code');  // id from array in env file
            // $table->date('dob');
            // $table->string('gender',16)->nullable();    // Do we need this??
            // $table->float('nominee_percentage');
            // $table->tinyInteger('relationship_type');   // id from array in env file
            // $table->tinyInteger('approval_status');   // id from array in env file
            // $table->boolean('is_active')->default(true);
            // $table->boolean('is_deceased')->default(false);
            // $table->rememberToken();
            // $table->timestamps();
            // $table->integer('created_by')->nullable();
            // $table->integer('modified_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contact_policies');
    }
}
