<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('users', function (Blueprint $table) {            
            $table->string('external_id')->after('id');
            $table->string('modified_by')->nullable();
            $table->string('created_by')->nullable();
            $table->float('nominee_percentage')->nullable();
            $table->tinyInteger('gender')->nullable();
            $table->string('suffix', 64)->nullable();
            $table->tinyInteger('title')->nullable();
            $table->tinyInteger('salutation')->nullable();
            $table->string('mobile_number')->nullable();  // with country code
            $table->foreignId('country_id_fk')->constrained('country_currency'); // zipcode ??
            $table->integer('points_available');
            $table->integer('points_used');
            $table->string('address')->nullable();
            $table->string('salary');
            $table->date('hire_date');
            $table->string('grade', 32);
            $table->string('employee_id',32)->unique();
            $table->string('lname', 64)->nullable();
            $table->string('mname', 64)->nullable();
            $table->string('fname', 64);
            $table->boolean('is_active')->default(true);


            // drop column from user table
            $table->dropColumn('name');            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('insurance_policy', function (Blueprint $table) {
            $table->dropColumn('si_factor');
            $table->dropColumn('sfdc_id');
            $table->dropColumn('modified_by');
            $table->dropColumn('created_by');
            $table->dropColumn('nominee_percentage');
            $table->dropColumn('gender');
            $table->dropColumn('suffix');
            $table->dropColumn('title');
            $table->dropColumn('salutation');
            $table->dropColumn('mobile_number');  // with country code
            $table->dropColumn('country_id_fk'); // zipcode ??
            $table->dropColumn('points_available');
            $table->dropColumn('points_used');
            $table->dropColumn('address');
            $table->dropColumn('salary');
            $table->dropColumn('hire_date');
            $table->dropColumn('grade');
            $table->dropColumn('employee_id');
            $table->dropColumn('lname');
            $table->dropColumn('mname');
            $table->dropColumn('fname');
            $table->dropColumn('is_active');
        });
    }
}
