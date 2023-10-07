<?php

namespace App\Api\Users;

use App\Http\Controllers\CrudController;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;

class UserController extends CrudController
{
    //
    public function __construct () {
        $this->model = new User();
        $this->modelQuery = $this->model;
        $this->validation = [
            "rules"=> [
                "first_name"=>["required"],
                "last_name"=>["required"],
                "email"=>["unique:users"],
                "department_id"=>["required"],
                "designation_id"=>["required"],
                'password'=>'required',
                'confirm_password'=>"required|same:password",
            ],
            "messages"=>[
                "password.confirmed"=>"Password and Confirm Password not match",
                "confirm_password.same"=>"Password and Confirm Password not match"
            ],
            "attributes"=>[]
        ];
    }
    
    public function index(Request $request) : object {
        return $this->modelQuery
            ->where('users.id',"<>",$request->user()->id)
            ->where('model_has_roles.role_id',3)
            ->join('model_has_roles',"model_has_roles.model_id","=","users.id")
            ->paginate($request->pageSize ? $request->pageSize : 5);
    }


    public function store(Request $request) : JsonResponse {

        $validation = Validator::make(
            $request->all(), 
            $this->validation['rules'], 
            $this->validation['messages'], 
            $this->validation['attributes']
        );

        if ($validation->fails()) {
            return response()->json([
                'errors' => $validation->errors(),
            ], 422);
        }

        $request->merge(['name' => $request->first_name . " " . $request->last_name]);

        $employee = Employee::create($request->only('name', 'department_id', 'designation_id'));
        // $employee = Employee::create([
        //     "name"=>$request->first_name . " " . $request->last_name,
        //     "designation_id" => $request->designation_id,
        //     "department_id" => $request->department_id,
        // ]);
        $request->merge(['employee_id'=>$employee->id]);

        $finalizeData = array_merge($request->except('department_id', 'designation_id'), $this->appendStore($request));

        $data = $this->model->create($finalizeData)->assignRole('employee');


        return response()->json($data, 201);
    }

    protected function appendStore(Request $request): array {
        return [
            "name"=>$request->first_name. " " . $request->last_name,
            "password"=>bcrypt($request->password)
        ];
    }

}
