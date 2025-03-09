<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function authenticate(Request $request)
    {

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
            $request->session()->regenerate();
            return response()->json(['status' => 'success','message' => 'Usuario autenticado'],200);
        }

        
        return response()->json(['message' => 'Las credenciales no son correctas'],422);

    

       
    }
}
