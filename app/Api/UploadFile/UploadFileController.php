<?php

namespace App\Api\UploadFile;

use App\Http\Controllers\CrudController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class UploadFileController extends CrudController
{
    //
    public function __construct()
    {
        $this->validation =[
            "rules"=>[
                "file"=>["required|mimes:png,jpg,gif,pdf"]
            ],
            "message"=>[],
            "attributes"=>[]
        ];
    }

    public function uploadFile(Request $request){
        $validate = Validator::make($request->all(),$this->validation["rules"]);

        if($validate->fails()){
            return response()->json([
                "errors"=> $validate->errors()
            ],422);
        }
        
        $files = $request->file('file');
        $errors = [];
        $success = [];
        foreach($files as $file){
            $path = "public/uploads/"+ auth()->id() +"/";

            try{
                if(!File::exists($path)){
                    $file->storeAs($path,$file->getClientOriginalName());
                    // $success = [];
                }
            }
            catch(\Exception $e){
                return response()->json(["error"=>"Unable to upload files"],500);
            }
        }

    }
}
