<?php

namespace App\Api\OutgoingDocument;

use Illuminate\Http\Request;

use App\Http\Controllers\CrudController;
use App\Models\OutgoingDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\Model;
use App\Models\OutgoingDocumentRoute;
use Carbon\Carbon;
use DateTime;

class OutgoingDocumentController extends CrudController
{
    //
    public function __construct()
    {   
        $this->model = new OutgoingDocument();
        $this->modelQuery = $this->model;
        $this->validation = [
            "rules"=>[
                'subject'=>["required"],
                'outgoing_category_id'=>["required"],
                'urgency_id'=>["required"],
            ],
            "message"=>[],
            "attributes"=>[]
        ];
    }

    ////////////////////////
    //// List of Record ////
    ////////////////////////
    public function index(Request $request) :object
    {
        
        $result = $this->model
        ->with([
            'createdBy','status','category','urgency','designation','department','division'
        ])
        ->when($request->filter, function($query) use($request){
            $query->where($request->filter,"LIKE","%".$request->filterBy."%");
        })
        ->when($request->specificDate, function($query) use($request){
            $query->whereDate('outgoing_documents.created_at',$request->specificDate);
        })
        ->when($request->filter, function($query) use($request){
            $query->where($request->filter,"LIKE","%".$request->filterBy."%");
        })
        ->when($request->from && $request->to, function ($query) use ($request) {
            $query->whereHas('documentDetails', function ($q) use ($request) {
                $q->whereBetween('outgoing_documents.created_at', [
                    $request->from,
                    date('Y-m-d', strtotime($request->to . "+ 1 day"))
                ]);
            });
        })
        ->when($request->sortBy && $request->sortOrder, function ($query) use ($request) {
            $query->orderBy($request->sortBy, $request->sortOrder);
        })
        ->orderBy('outgoing_documents.created_at','DESC')
        ->paginate($request->pageSize);

        return response()->json($result,200);
    }




    ////////////////////////
    ////// New Record //////
    ////////////////////////
    public function store(Request $request) : JsonResponse {
        
        $validation = Validator::make(
            $request->all(), 
            $this->validation['rules'],
            $this->validation['message'],
        );

        if ($validation->fails()) {
            return response()->json([
                'errors' => $validation->errors(),
            ], 422);
        }

        $finalizeData = array_merge($request->all(), $this->appendStore($request));
        $data = $this->model->create($finalizeData);

        $files = $request->file;
        
        if($files){
            foreach($files as $file){
                $path = public_path('storage/public/outgoing/'.$data->document_no.'/'.$file->getClientOriginalName());

                try{
                    if(!File::exists($path)){
                        $file->storeAs('public/outgoing/'.$data->document_no.'/',$file->getClientOriginalName());
                        // $success = [];
                    }
                }
                catch(\Exception $e){
                    return response()->json(["error"=>"Unable to upload files"],500);
                }
            }
        }

        $details = $this->model
        ->select([
            'outgoing_documents.document_no','outgoing_documents.subject','outgoing_documents.description','outgoing_documents.hashcode','outgoing_documents.created_at',

            'outgoing_document_categories.id as document_category_id','outgoing_document_categories.name as document_category_name',
            'document_urgencies.id as urgency_id','document_urgencies.name as urgency_name',
            'users.id as created_by_id','users.name as created_by_name',
            'designations.id as designation_id','designations.name as designation_name',
            'departments.id as department_id','departments.name as department_name',
            'divisions.id as division_id','divisions.name as division_name',
            'document_statuses.id as document_status_id','document_statuses.name as document_status_name',
        ])
        ->leftJoin('document_statuses','document_statuses.id','outgoing_documents.document_status_id')
        ->leftJoin('divisions','divisions.id','outgoing_documents.division_id')
        ->leftJoin('departments','departments.id','outgoing_documents.department_id')
        ->leftJoin('designations','designations.id','outgoing_documents.designation_id')
        ->leftJoin('users','users.id','outgoing_documents.created_by')
        ->leftJoin('document_urgencies','document_urgencies.id','outgoing_documents.urgency_id')
        ->leftJoin('outgoing_document_categories','outgoing_document_categories.id','outgoing_documents.outgoing_category_id')
        ->where('outgoing_documents.hashcode','=',$data->hashcode)
        ->first();

        return response()->json($details, 201);
    }

    protected function appendStore(Request $request) : array {

        return [
            'hashcode'=>$this->generateDocumentHash(64),
            'document_no'=> $this->OutgoingDocumentNumber(),
            'created_by'=>auth()->id(),
            'designation_id'=>$request->user()->employee->designation_id,
            'department_id'=>$request->user()->employee->department_id,
            'division_id'=>$request->user()->employee->division_id,
        ];
    }

    protected function generateDocumentHash($length = 10) {

        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        if(OutgoingDocument::where('hashcode',$randomString)->count() > 0){
            $this->generateDocumentHash($length);
        }else{
            return $randomString;
        }
    }

    protected function OutgoingDocumentNumber(){

        $last = OutgoingDocument::max('document_no');
        if(OutgoingDocument::count() < 1){
            $max = 0;
        }else{
            $max = explode("-", $last)[1];
        }
   
       $increment = (int)$max + 1;
       $len = strlen($increment);
       $documentNo = date('Y')."-";

       for($i = 1; $i <= (10 - $len); $i++){
           $documentNo .="0";
       }

       $documentNo .= $increment;
       return $documentNo;
    }


    ////////////////////////
    // ShowSpecificRecord //
    ////////////////////////
    public function show($hashcode): JsonResponse {

        $data = $this->model->where('hashcode','=',$hashcode);

        if (!$data->first()) {
            return response()->json('Not found', 404);
        }

        $documentDetails = $data
        ->with(['routes','documentComments','createdBy','status','category','urgency','designation','department','division'])
        ->first();
        return response()->json($documentDetails, 200);
    }

    
    
    //////////////////////////
    ////  Update Record  ////
    /////////////////////////
    public function update(Request $request, $hashcode): JsonResponse {

        $validation = Validator::make(
            $request->all(), 
            $this->validation['rules'], 
            $this->validation['message'], 
        );

        if ($validation->fails()) {
            return response()->json([
                'errors' => $validation->errors(),
            ], 422);
        }

        $finalizeData = array_merge($request->except(['file','to_delete']), $this->appendUpdate($request));

        $data = $this->model->where('hashcode',$hashcode)->first();

        $updated = $data->update($finalizeData);
        
        
        $toDelete = $request->to_delete;

        if($toDelete){
            foreach($toDelete as $file){
                $path = public_path('storage//outgoing//'.$data->document_no.'//'.$file);
                
                try{
                    if(File::exists($path)){
                        File::delete($path);
                    }
                }
                catch(\Exception $e){
                    return response()->json(["error"=>"Unable to delete files"],500);
                }
            }
        }

        // Save Attachment
        $files = $request->file;
        if($files){
            foreach($files as $file){
                $path = public_path('storage//outgoing/'.$data->document_no.'//'.$file->getClientOriginalName());
                try{
                    if(!File::exists($path)){
                        $file->storeAs('public//outgoing/'.$data->document_no.'//',$file->getClientOriginalName());
                    }
                }
                catch(\Exception $e){
                    return response()->json(["error"=>"Unable to upload files"],500);
                }
            }
        }

         $documentDetails = $this->model->where('hashcode',$hashcode)
        ->with(['routes','createdBy','status','category','urgency','designation','department','division'])
        ->first();

        return response()->json($documentDetails, 200);
    }

    protected function appendUpdate(Request $request): array {
        return [];
    }


    public function markAsComplete (Request $reuqest, $hashcode){
        $doc = $this->model->where('hashcode',$hashcode)->first();

        if(!$doc){
            return response()->json(['message'=>"Document not found"],422);
        }

        if($doc->document_status_id == 3){
            return response()->json(['message'=>"The document routing is already complete."],422);
        }

        $doc->update(['document_status_id'=>3]); //  Mark document as completed routing

        return response()->json($doc,200);
    }


    //////////////////////////
    ////  Delete Record  ////
    /////////////////////////
    public function destroy($hashcode) : JsonResponse {
        try{
            $data = $this->model->where('hashcode',$hashcode)->first();
            $document_no = $data->document_no;

            if (!$data) {
                return response('Not found', 404);
            }

            $path = public_path('storage//'.$document_no);
            if(File::exists($path)){
                File::deleteDirectory($path);
            }

            $data->delete();
            $this->onDestroy($data);
            
            return response()->json(null,204);

        } catch (\Exception $e) {
            throw $e->getMessage();
        }
    }

    protected function onDestroy(Model $model): void {
        return;
    }


}
