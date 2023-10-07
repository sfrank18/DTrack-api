<?php

namespace App\Api\Departments;

use App\Http\Controllers\CrudController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\Department;
use Illuminate\Validation\Rule;

class DepartmentsController extends CrudController
{
    
    public function __construct () {
        $this->model = new Department();
        $this->modelQuery = $this->model;
        $this->validation = [
            "rules"=>[
                "name"=>["required",
                Rule::unique('departments')->ignore(request('id'))->where(function($query) {
                    return $query->where('name',request("name"))
                            ->where('division_id',request('division_id'));
                }),    
            ],
                
                "division_id"=>"required",
            ],
            "message"=>[
                'name.unique'=> request("name") . " department already exist"
            ],
            "attributes"=>[]
        ];
    }

    public function index(Request $request) :object{

        $result = $this->model
        ->select('departments.*', 'divisions.name as division_name')
        ->when($request ,function($query) use($request) {
            if ($request->filter) {
                $query->where('departments.name',"LIKE",$request->filter."%");
            }
            if($request->sortBy && $request->sortOrder){
                $query->orderBy($request->sortBy,$request->sortOrder);
            }
        })
        ->with('user')
        ->leftjoin('divisions','divisions.id','departments.division_id')
        ->paginate($request->pageSize);

        return response()->json($result,200);
    }
    
    public function store(Request $request) : JsonResponse {
        return response()->json($request, 201);
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

        return response()->json($data, 201);
    }

    protected function appendStore(Request $request) : array {
        return ['created_by'=>auth()->id()];
    }
}
