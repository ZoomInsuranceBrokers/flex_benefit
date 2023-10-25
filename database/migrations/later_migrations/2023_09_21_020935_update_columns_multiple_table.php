<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateColumnsMultipleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('insurance_category', function (Blueprint $table) {
            $table->string('is_active')->default(true)->after('tagline');
        });
        Schema::table('insurance_subcategory', function (Blueprint $table) {
            $table->string('is_active')->default(true)->after('core_multiple');
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
            $table->dropColumn('is_active');
        });
        Schema::table('insurance_subcategory', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
}
