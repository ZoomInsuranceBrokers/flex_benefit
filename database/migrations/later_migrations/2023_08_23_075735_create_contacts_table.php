<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id()->from(100);
            $table->string('sfdc_id');
            $table->string('fname', 64);
            $table->string('mname', 64)->nullable();
            $table->string('lname', 64)->nullable();
            $table->string('employee_id',32)->unique();
            $table->string('email',128)->unique();
            $table->string('grade', 32);
            $table->date('hire_date');
            $table->string('address');
            $table->foreignId('country_id_fk')->constrained('country_currency'); // zipcode ??
            $table->string('mobile_number')->unique();  // with country code
            $table->tinyInteger('salutation')->nullable();
            $table->tinyInteger('title')->nullable();
            $table->string('suffix', 64)->nullable();
            $table->tinyInteger('gender')->nullable();
            $table->string('password');
            $table->float('nominee_percentage');
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
            $table->string('created_by')->nullable();
            $table->string('modified_by')->nullable();
            $table->timestamp('email_verified_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contacts');
    }
}
