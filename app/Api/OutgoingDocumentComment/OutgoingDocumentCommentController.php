<?php

namespace App\Api\OutgoingDocumentComment;

use App\Http\Controllers\CrudController;
use Illuminate\Http\Request;
use App\Models\OutgoingDocumentComment;
use App\Models\OutgoingDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class OutgoingDocumentCommentController extends CrudController
{
    //
    public function __construct()
    {
        $this->model = new OutgoingDocumentComment();
        $this->modelQuery = $this->model;
        $this->validation = [
            'rules'=>[
                'comment'=>"required"
            ],
            'message'=>[],
            'attributes'=>[],
        ];
    }


    public function store (Request $request) : JsonResponse {
        
        $validate = Validator::make($request->all(),$this->validation['rules']);

        if($validate->fails()){
            return response()->json(['errors'=>$validate->errors()],422);
        }

        $document = OutgoingDocument::where('hashcode', $request->hashcode)->first();

        if(!$document){
            return response()->json(['error'=>"unable to add comment, document does not exist"]);
        }

        $finalizeData = array_merge($request->all(), $this->onStoreAppend($request, $document->id));

        $data = $this->model->create($finalizeData);

        return response()->json(["message"=>"comment added succesfully"],200);
    }

    protected function onStoreAppend($request, $document_id) : array {
        return [
            'document_id'=>$document_id,
            'comment_by'=>$request->user()->id,
            'designation_id'=>$request->user()->employee->designation_id,
            'department_id'=>$request->user()->employee->department_id,
            'division_id'=>$request->user()->employee->division_id,
        ];
    } 

    

}
