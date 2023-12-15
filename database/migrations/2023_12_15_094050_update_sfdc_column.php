<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSfdcColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->string('external_id')->nullable()->after('id');            
            $table->dropColumn('sfdc_id');            
        });
        Schema::table('dependent', function (Blueprint $table) {
            $table->string('external_id')->nullable()->after('id');            
            $table->dropColumn('sfdc_id');            
        });
        Schema::table('financial_years', function (Blueprint $table) {
            $table->string('external_id')->nullable()->after('id');            
            $table->dropColumn('sfdc_id');            
        });
        Schema::table('insurance_category', function (Blueprint $table) {
            $table->string('external_id')->nullable()->after('id');            
            $table->dropColumn('sfdc_id');            
        });
        Schema::table('insurance_policy', function (Blueprint $table) {
            $table->string('external_id')->nullable()->after('id');            
            $table->dropColumn('sfdc_id');            
        });
        Schema::table('insurance_subcategory', function (Blueprint $table) {
            $table->string('external_id')->nullable()->after('id');            
            $table->dropColumn('sfdc_id');            
        });
        Schema::table('map_financial_year_policy', function (Blueprint $table) {
            $table->string('external_id')->nullable()->after('id');            
            $table->dropColumn('sfdc_id');            
        });
        Schema::table('users', function (Blueprint $table) {
            $table->string('external_id')->nullable()->after('id');            
            $table->dropColumn('sfdc_id');            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn('external_id');
            $table->string('sfdc_id')->nullable()->after('id'); 
        });
    }
}
