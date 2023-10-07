<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutgoingDocumentActionLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outgoing_document_action_logs', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('route_id')->unsigned()->nullable();
            $table->foreign("route_id")->references('id')->on('outgoing_document_routes');

            $table->bigInteger('document_id')->unsigned()->nullable();
            $table->foreign("document_id")->references('id')->on('outgoing_documents');

            $table->string('hashcode');

            $table->bigInteger('action_id')->unsigned()->nullable();
            $table->foreign("action_id")->references('id')->on('document_actions');

            $table->bigInteger('perform_by')->unsigned()->nullable();
            $table->foreign("perform_by")->references('id')->on('users');

            $table->bigInteger('designation_id')->unsigned()->nullable();
            $table->foreign("designation_id")->references('id')->on('designations');

            $table->bigInteger('department_id')->unsigned()->nullable();
            $table->foreign("department_id")->references('id')->on('departments');

            $table->bigInteger('division_id')->unsigned()->nullable();
            $table->foreign("division_id")->references('id')->on('divisions');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('outgoing_document_action_logs');
    }
}
