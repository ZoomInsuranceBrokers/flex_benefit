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
            $table->string('modified_by')->nullable()->after('sfdc_id');
            $table->string('created_by')->nullable()->after('sfdc_id');
            $table->float('nominee_percentage')->nullable()->after('sfdc_id');
            $table->tinyInteger('gender')->nullable()->after('sfdc_id');
            $table->string('suffix', 64)->nullable()->after('sfdc_id');
            $table->tinyInteger('title')->nullable()->after('sfdc_id');
            $table->tinyInteger('salutation')->nullable()->after('sfdc_id');
            $table->string('mobile_number')->nullable()->after('sfdc_id');  // with country code
            $table->foreignId('country_id_fk')->constrained('country_currency')->after('sfdc_id'); // zipcode ??
            $table->integer('points_available')->after('sfdc_id');
            $table->integer('points_used')->after('sfdc_id');
            $table->string('address')->nullable()->after('sfdc_id');
            $table->string('salary')->after('sfdc_id');
            $table->date('hire_date')->after('sfdc_id');
            $table->string('grade', 32)->after('sfdc_id');
            $table->string('employee_id',32)->unique()->after('sfdc_id');
            $table->string('lname', 64)->nullable()->after('sfdc_id');
            $table->string('mname', 64)->nullable()->after('sfdc_id');
            $table->string('fname', 64)->after('sfdc_id');
            $table->boolean('is_active')->after('sfdc_id')->default(true);


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
