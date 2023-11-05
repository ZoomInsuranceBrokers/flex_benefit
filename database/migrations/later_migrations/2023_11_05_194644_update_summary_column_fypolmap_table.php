<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSummaryColumnFypolmapTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('map_user_fypolicy', function (Blueprint $table) {
            $table->text('encoded_summary')->nullable()->after('points_used');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('map_user_fypolicy', function (Blueprint $table) {
            $table->dropColumn('encoded_summary');
        });
    }
}
