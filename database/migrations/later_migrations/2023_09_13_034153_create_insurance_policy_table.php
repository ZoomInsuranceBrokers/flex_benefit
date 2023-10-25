<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInsurancePolicyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('insurance_policy', function (Blueprint $table) {
            $table->id()->from(1000);
            $table->string('external_id');        // sfdc ID
            $table->string('name', 256);
            $table->double('sum_insured',16,2);
            $table->foreignId('ins_subcategory_id_fk')->constrained('insurance_subcategory');;  // id from array in env file
            $table->mediumText('description')->nullable();
            $table->float('price_tag',3,4)->nullable();
            $table->integer('points')->nullable();
            $table->integer('extra_assured_amount')->nullable();
            $table->string('dependent_structure')->nullable();
            $table->boolean('is_parent_sublimit')->default(false);
            $table->integer('parent_sublimit_amount')->nullable();
            $table->integer('insurer_cost')->nullable();
            $table->string('lumpsum_amount')->nullable();
            $table->integer('replacement_of_policy_id')->nullable();    // if replacing other policy, then store ID of that policy
            $table->string('replacement_of_policy_sfdc_id')->nullable();    // if replacing other policy, then store ID of that policy
            $table->foreignId('currency_id_fk')->constrained('currency');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->string('created_by')->nullable();
            $table->string('modified_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('insurance_policy');
    }
}
