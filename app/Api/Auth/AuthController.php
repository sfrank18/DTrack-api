<?php

namespace App\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //
    public function login(Request $request){

        // validate user input
        $validate = Validator::make($request->all(),[
            "email"=>"required|email",
            "password"=>"required"
        ]);

        if($validate->fails()){
            return response()->json(["errors"=>$validate->errors()],422);
        }
        
        // attemp to login
        $attempt = auth()->attempt($request->only('email','password'));

        if(!$attempt){
            return response()->json(["message"=>"Invalid Email Address or Password"],422);
        }

        $details = null;
        $user = $request->user();
        
        return response()->json([
            "token_type"=>"Bearer ",
            "token"=>$user->createToken('auth_token')->plainTextToken,
            "user"=>$user
        ],200);
    }

    public function user(Request $request){

        $user = $request->user();
        
        return response()->json($user,200);
        
    }

    public function changePassword (Request $request) {
        
        $validate = Validator::make($request->all(),[
            'password'=>'required|current_password',
            'new_password'=>"required|confirmed",
            'new_password_confirmation' => 'required|same:new_password'
        ],[
            "new_password.confirmed"=>"New password and confirm password not match",
            "new_password_confirmation.same"=>"New password and confirm password not match"
        ]);

        if($validate->fails()){
            return response()->json(['errors' => $validate->errors()], 422);
        }

        if(Hash::check($request->new_password,auth()->user()->password))
        {
            return response()
             ->json([
                    'errors' => [
                        'new_password' => [
                            "Your new password cannot be the same as your current password."
                        ]
                    ]
                        ],422
            );
        }
        
        $request->user()->update([
            'password'=>bcrypt($request->new_password)
        ]);

        return response(null)->setStatusCode(204);
    }
}
