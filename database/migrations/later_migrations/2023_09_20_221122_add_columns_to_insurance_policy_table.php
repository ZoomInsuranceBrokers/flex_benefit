<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToInsurancePolicyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('insurance_policy', function (Blueprint $table) {
            $table->float('si_factor')->nullable()->after('currency_id_fk');
            $table->boolean('is_base_plan')->default(false)->after('si_factor');
            $table->boolean('is_default_selection')->default(false)->after('is_base_plan');
            $table->integer('base_plan_id')->nullable()->after('is_default_selection');
            $table->string('base_plan_id_sfdc')->nullable()->after('base_plan_id');
            $table->string('base_plan_text')->nullable()->after('base_plan_id_sfdc');
            $table->string('base_plan_sum_assured_text')->nullable()->after('base_plan_text');
            $table->boolean('is_multi_selectable')->default(false)->after('base_plan_sum_assured_text');
            $table->boolean('is_point_value_based')->default(false)->after('is_multi_selectable');        //for flex benefits, it will be true 
            $table->boolean('show_value_column')->default(false)->after('is_point_value_based');        //for flex benefits value based benefit, it will be true 
            //
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
            $table->dropColumn('is_base_plan');
            $table->dropColumn('is_default_selection');
            $table->dropColumn('base_plan_id');
            $table->dropColumn('base_plan_id_sfdc');
            $table->dropColumn('base_plan_text');
            $table->dropColumn('base_plan_sum_assured_text');
            $table->dropColumn('is_multi_selectable');
            $table->dropColumn('is_point_value_based'); 
            $table->dropColumn('show_value_column');
        });
    }
}
