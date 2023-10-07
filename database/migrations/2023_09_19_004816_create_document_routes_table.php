<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outgoing_document_routes', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('document_id')->unsigned()->nullable();
            $table->foreign("document_id")->references('id')->on('outgoing_documents');

            $table->string('hashcode');
            
            $table->bigInteger('received_department_id')->unsigned()->nullable();
            $table->foreign("received_department_id")->references('id')->on('departments');

            $table->bigInteger('received_division_id')->unsigned()->nullable();
            $table->foreign("received_division_id")->references('id')->on('divisions');

            $table->bigInteger('received_by')->unsigned()->nullable();
            $table->foreign("received_by")->references('id')->on('users');

            $table->bigInteger('received_designation_id')->unsigned()->nullable();
            $table->foreign("received_designation_id")->references('id')->on('designations');

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
        Schema::dropIfExists('outgoing_document_routes');
    }
}
