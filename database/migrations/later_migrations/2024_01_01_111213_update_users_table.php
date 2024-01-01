<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('dob')->nullable()->after('grade_id_fk');            
            $table->tinyInteger('is_enrollment_submitted')->default(false)->after('nominee_percentage');            
            $table->timestamp('enrollment_submit_date')->nullable()->after('is_enrollment_submitted');         
            $table->bigInteger('submission_by')->nullable()->after('enrollment_submit_date');         
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('dob');
            $table->dropColumn('is_enrollment_submitted');
            $table->dropColumn('enrollment_submit_date');
            $table->dropColumn('submission_by');
        });
    }
}
