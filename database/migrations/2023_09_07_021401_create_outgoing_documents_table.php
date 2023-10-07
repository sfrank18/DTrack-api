<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutgoingDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outgoing_documents', function (Blueprint $table) {
            $table->id();
            $table->string('document_no')->unique();
            $table->string("subject");
            $table->text("description")->nullable();
            $table->string('hashcode')->nullable()->unique();

            $table->bigInteger('outgoing_category_id')->unsigned()->nullable();
            $table->foreign("outgoing_category_id")->references('id')->on('outgoing_document_categories');

            $table->bigInteger('urgency_id')->unsigned()->nullable();
            $table->foreign("urgency_id")->references('id')->on('document_urgencies');

            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->foreign("created_by")->references('id')->on('users');

            $table->bigInteger('designation_id')->unsigned()->nullable();
            $table->foreign("designation_id")->references('id')->on('designations');

            $table->bigInteger('department_id')->unsigned()->nullable();
            $table->foreign("department_id")->references('id')->on('departments');
            
            $table->bigInteger('division_id')->unsigned()->nullable();
            $table->foreign("division_id")->references('id')->on('divisions');
            
            $table->bigInteger('document_status_id')->unsigned()->default(1);
            $table->foreign("document_status_id")->references('id')->on('document_statuses');

            $table->tinyInteger('is_active')->default(1);
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
        Schema::dropIfExists('outgoing_documents');
    }
}
