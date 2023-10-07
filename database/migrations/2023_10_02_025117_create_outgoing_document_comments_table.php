<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutgoingDocumentCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outgoing_document_comments', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('document_id')->unsigned()->nullable();
            $table->foreign("document_id")->references('id')->on('outgoing_documents');

            $table->string('hashcode');
            
            $table->string('comment');

            $table->bigInteger('route_id')->unsigned()->nullable();
            $table->foreign("route_id")->references('id')->on('outgoing_document_routes');

            $table->bigInteger('comment_by')->unsigned()->nullable();
            $table->foreign("comment_by")->references('id')->on('users');

            $table->bigInteger('designation_id')->unsigned()->nullable();
            $table->foreign("designation_id")->references('id')->on('designations');

            $table->bigInteger('department_id')->unsigned()->nullable();
            $table->foreign("department_id")->references('id')->on('departments');

            $table->bigInteger('division_id')->unsigned()->nullable();
            $table->foreign("division_id")->references('id')->on('divisions');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('outgoing_document_comments');
    }
}
