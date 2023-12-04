<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUserGradeFypmapSubmissionColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // drop column from user table
            $table->dropColumn('grade'); 
        });

        Schema::table('users', function (Blueprint $table) {
            $table->integer('grade_id_fk')->nulllable()->default(1)->after('employee_id'); 
        });

        Schema::table('map_user_fypolicy', function (Blueprint $table) {
            $table->string('is_submitted')->default(false)->after('encoded_summary');
            $table->integer('email_id')->nullable()->after('is_submitted');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
