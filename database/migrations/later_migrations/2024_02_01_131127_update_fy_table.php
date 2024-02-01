<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('financial_years', function (Blueprint $table) {
            $table->timestamp('last_enrollment_date')->nullable()->after('end_date');
        });
        Schema::table('accounts', function (Blueprint $table) {
            $table->tinyInteger('is_active')->default(0)->after('mobile_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('financial_years', function (Blueprint $table) {
            $table->dropColumn('last_enrollment_date');
        });
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
}
