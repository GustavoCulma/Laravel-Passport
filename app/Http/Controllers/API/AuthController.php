<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //registrar usuarios
    public function register(Request $request)
    {
        //validar la data
        $validateData = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ]);

        //encriptar la contraseña
        $validateData['password'] = Hash::make($request->password);

        // almacenar el usuario en la base de datos
        $user = User::create($validateData);

        //generar el AccesToken para el usuario
        $accessToken = $user->createToken('authToken')->accessToken;

        //generar respuesta para el usuario
        return response([
            'user'=> $user,
            'access_token'=> $accessToken,
        ]);
    }

    //validar usuario y contraseña que esta enviando el usuario
    public function login(Request $request)
    {
        //validar la data
        $loginData = $request->validate([
            
            'email' => 'email|required',
            'password' => 'required',
        ]);

        //validar si el usuario existe en la base de datos
        if (!auth()->attempt($loginData)) {
            return response(['message'=>'Invalid Credentials']);
        }
        

        //generar el AccesToken para el usuario
        $accessToken = auth()->user()->createToken('authToken')->accessToken;

        //generar respuesta para el usuario
        return response([
            'user'=> auth()->user(), 
            'access_token'=> $accessToken,

        ]);
    }
}
