<?php

namespace App\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class UserController extends Controller
{
    public function login(Request $request){

        $validator = validator()->make($request->all(),[
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if($validator->fails()){
            // return $validator->errors();
            return response()->json($validator->errors(), 422);
        }

        $credentials = $validator->valid();


        //Autenticar el usuario
        if(Auth::attempt($credentials)){
            $token = Auth::user()->createToken('authToken')->plainTextToken;
            session()->put('token',$token);
            return response()->json(
                [
                    'isLoggedIn'=> true,
                    'token' => $token,
                    'user' => auth()->user()
                ]
            );
        }

        
        return response()->json(['message' => 'Las credenciales no son correctas'],422);

    

    }

    public function logout(Request $request){

            $request->user()->tokens()->delete();
   
    
      
        session()->flush();
        return response()->json('Sesión cerrada con exito');
    }

    public function checkToken(){
        try{
            [$id, $token] = explode('|',request('token'));
            $tokenHash = hash('sha256', $token);

            $tokenModel = PersonalAccessToken::where('token', $tokenHash)->first();

            if($tokenModel){
                return response()->json(
                    [
                        'isLoggedIn'=> true,
                        'token' => $token,
                        'user' => auth()->user
                    ]
                );
            }
        }catch(Exception $e){
            return response()->json('invalid User', 422);
        }
    }
}
