<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;

class CrudController extends Controller
{

    protected $model;

    protected $modelQuery;

    protected $validation = [
        'rules' => [],
        'message' => [],
        'attributes' => []
    ];
    
    public function index(Request $request) : object {
        return $this->modelQuery
            ->when($request, function($query) use($request) {
                $this->search($query, $request);
            })
            ->paginate($request->pageSize ? $request->pageSize : 10);
    }

    public function search($query, $request){
        return;
    }

    public function store(Request $request) : JsonResponse {

        $validation = Validator::make(
            $request->all(), 
            $this->validation['rules'], 
            $this->validation['message'], 
            $this->validation['attributes']
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
        return [];
    }   

    protected function onStore(Model $model): void {
        return;
    }

    public function update(Request $request, $id): JsonResponse {
        $validation = Validator::make(
            $request->all(), 
            $this->validation['rules'], 
            $this->validation['message'], 
            $this->validation['attributes']
        );

        if ($validation->fails()) {
            return response()->json([
                'errors' => $validation->errors(),
            ], 422);
        }

        $finalizeData = array_merge($request->all(), $this->appendUpdate($request));

        $this->model->find($id)->update($finalizeData);

        $data = $this->model->find($id);

        $this->onUpdate($data);

        return response()->json($data, 200);
        
    }

    protected function appendUpdate(Request $request): array {
        return [];
    }

    protected function onUpdate(Model $model): void {
        return;
    }

    public function show($id): JsonResponse {
        $data = $this->model->find($id);

        if (!$data) {
            return response('Not found', 404);
        }

        return response()->json($this->model->find($id), 200);
    }

    public function destroy($id): JsonResponse {
        try{
            $data = $this->model->find($id);

            if (!$data) {
                return response('Not found', 404);
            }

            $data->delete();
            $this->onDestroy($data);

            return response()->json(null, 204);

        } catch (\Exception $e) {
            throw $e->getMessage();
        }
    }

    protected function onDestroy(Model $model): void {
        return;
    }

}
