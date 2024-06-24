<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSupportedDocumentToDependentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dependent', function (Blueprint $table) {
            $table->text('supported_document')->nullable()->after('is_life_event');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dependent', function (Blueprint $table) {
            $table->dropColumn('supported_document');
        });
    }
}
