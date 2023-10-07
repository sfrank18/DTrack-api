<?php

namespace App\Api\OutgoingDocumentRoute;

use App\Http\Controllers\CrudController;
use Illuminate\Http\Request;
use App\Models\OutgoingDocumentRoute;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use App\Models\OutgoingDocument;
use Illuminate\Support\Facades\DB;

class OutgoingDocumentRoutesController extends CrudController
{
    //
    public function __construct()
    {
        $this->model = new OutgoingDocumentRoute();
        $this->modelQuery = $this->model;
        $this->validation = [
            "rules"=>[
                'hashcode'=>["required"],
            ],
            "message"=>[],
            "attributes"=>[]
        ];
    }

    public function index(Request $request) : object {

        $list = $this->model
            ->with([
                'documentDetails','documentDetails.department','documentDetails.status','documentDetails.category','documentDetails.urgency','documentDetails.routes',
                'receivedDepartment','receivedBy','receivedDesignation','receivedDivision'
            ])
            ->when($request->filter == "outgoing_documents.document_no", function($query) use($request){
                $query->wherehas('documentDetails',function($q) use($request){
                    $q->where('document_no','LIKE','%'.$request->filterBy.'%');
                });
            })
            ->when($request->filter == "outgoing_documents.subject", function($query) use($request){
                $query->wherehas('documentDetails',function($q) use($request){
                    $q->where('subject','LIKE','%'.$request->filterBy.'%');
                });
            })
            ->when($request->filter == "outgoing_documents.department_name", function($query) use($request){
                $query->wherehas('documentDetails.department',function($q) use($request){
                    $q->where('departments.name','LIKE','%'.$request->filterBy.'%');
                });
            })
            ->when($request->filter == "outgoing_document_routes.received_by", function($query) use($request){
                $query->wherehas('receivedBy',function($q) use($request){
                    $q->where('users.name','LIKE','%'.$request->filterBy.'%');
                });
            })
            ->when($request->specificDate, function($query) use($request){
                $query->whereDate('outgoing_document_routes.created_at',$request->specificDate);
            })
            ->when($request->from && $request->to, function ($query) use ($request) {
                $query->whereBetween('outgoing_document_routes.created_at', [
                    $request->from,
                    date('Y-m-d', strtotime($request->to . "+ 1 day"))
                ]);
            })
            ->when($request->sortBy && $request->sortOrder, function ($query) use ($request) {
                $query->orderBy($request->sortBy, $request->sortOrder);
            })
            ->whereHas('receivedDepartment', function ($query) use ($request) {
                $query->where('id', $request->user()->employee->department_id);
            })
            ->paginate($request->pageSize);
        
        return response()->json($list,200);
    }

    public function store(Request $request) : JsonResponse{

        $validation = Validator::make($request->all(), $this->validation['rules']);

        if($validation->fails()){
            return response()->json([
                'errors' => $validation->errors(),
            ], 422);
        }

        $document = OutgoingDocument::where('hashcode', $request->hashcode)->first();

        // check if document exist
        if(!$document){
            return response()->json(["message"=>"document not found"],404);
        }

        // Check if document is already scanned on current department
        $latest = $this->model->where('hashcode',$request->hashcode)->orderBy('created_at', 'DESC')->first();

        if($latest){
            if($latest->received_department_id == $request->user()->employee->department_id){
                return response()->json(["message"=>"The document has already been scanned in your department"],200);
            }
        }
        else{
            // Check if the 1st person scanning  are from the same department
            if($document->department_id === $request->user()->employee->department_id){
                return response()->json(["message"=>"Your office is the origin of this document."],200);
            }
        }

        // Check if document routing is already finished/completed
        if($document->outgoing_document_status==3){
            return response()->json(["message"=>"Routing for this document is completed."],422);
        }


        $document->update([
            'document_status_id'=>2
        ]);

        $finalizeData = array_merge($request->all(), $this->onStoreAppend($request, $document->id));
        $recieved =$this->model->create($finalizeData);

        return response()->json($recieved, 200);
    }

    protected function onStoreAppend($request, $id) : array{
        return [
            'document_id'=>$id,
            "received_by"=>$request->user()->id,
            'received_designation_id'=>$request->user()->employee->designation_id,
            'received_department_id'=>$request->user()->employee->department_id,
            'received_division_id'=>$request->user()->employee->division_id,
        ];
    }
    
}
