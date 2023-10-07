<?php

namespace App\Api\Divisions;

use App\Http\Controllers\CrudController;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use App\Models\Division;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;


class DivisionsController extends CrudController
{
    public function __construct () {

        $this->model = new Division();
        $this->modelQuery = $this->model;
        $this->validation = [
            'rules' => [
                "name" => ["required",
                    Rule::unique('divisions')->ignore(request("id")),
                ]
            ],
            'message' => [
                "name.unique" => request("name") . " Division already exist"
            ],
            'attributes' => []
        ];
        
    }

    public function index(Request $request) : object {
        $result = $this->model
        ->when($request ,function($query) use($request) {
            if ($request->filter) {
                $query->where('name',"LIKE",$request->filter."%");
            }
            if($request->sortBy && $request->sortOrder){
                $query->orderBy($request->sortBy,$request->sortOrder);
            }
        })
        ->with('user')
        ->paginate($request->pageSize);

        return response()->json($result,200);
    }

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

        return response()->json($data, 201);
    }

    protected function appendStore(Request $request) : array {
        return ['created_by'=>auth()->id()];
    }

}
