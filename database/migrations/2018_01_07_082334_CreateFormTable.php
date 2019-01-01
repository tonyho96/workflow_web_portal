<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forms', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('raised_by_user_id');
            $table->string('contact_no');
            $table->integer('change_type');
            $table->string('change_title');
            $table->integer('proposed_change_owner_user_id');
            $table->string('date_raised');
            $table->integer('business_priority');
            $table->date('change_start_date');
            $table->time('change_start_time');
            $table->date('change_end_date');
            $table->time('change_end_time');
            $table->text('proposed_change');
            $table->text('reason');
            $table->text('risk_assessment');
            $table->text('rollback_strategy');
            $table->text('test_plan');
            $table->text('authorisation_signature');
            $table->text('authorisation_signature_date');
            $table->text('completion_notes')->nullable();
            $table->text('completion_signature')->nullable();
            $table->text('completion_signature_date')->nullable();
            $table->integer('author_user_id');
            $table->string('reference');
            $table->string('status');
            $table->string('complete_status');
            $table->date('planned_date');
            $table->text('approved_rejected_reason')->nullable();
            $table->date('approved_rejected_date')->nullable();
            $table->timestamps();
            $table->integer('approved_rejected_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('forms');

    }
}
