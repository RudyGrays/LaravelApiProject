<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    
    public function register(Request $request){

       
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ];
        
        $messages = [
            'first_name.required' => 'Поле "Имя" обязательно для заполнения.',
            'first_name.string' => 'Поле "Имя" должно быть строкой.',
            'first_name.max' => 'Поле "Имя" не может содержать более :max символов.',
            'last_name.required' => 'Поле "Фамилия" обязательно для заполнения.',
            'last_name.string' => 'Поле "Фамилия" должно быть строкой.',
            'last_name.max' => 'Поле "Фамилия" не может содержать более :max символов.',
            'email.required' => 'Поле "Email" обязательно для заполнения.',
            'email.email' => 'Поле "Email" должно быть в формате email.',
            'email.unique' => 'Такой email уже существует.',
            'password.required' => 'Поле "Пароль" обязательно для заполнения.',
            'password.string' => 'Поле "Пароль" должно быть строкой.',
            'password.min' => 'Поле "Пароль" должно содержать не менее :min символов.',
        ];

        try{
            $request->validate($rules, $messages);
        }catch(ValidationException $e){
            $errors = $e->validator->errors()->all();
            return response()->json(['message' => $errors], 422);
        }
        
        $user = User::create([
            'first_name'=> $request['first_name'],
            'last_name'=> $request['last_name'],
            'email'=> $request['email'],
            'password'=>bcrypt($request['password']),
        ]);
        
        $token = $user->createToken('secret123')->plainTextToken;
        
        return response(['Success'=>True, 'token'=> $token], 200);
    }


    public function login(Request $request) {

        $rules = [
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ];
        $messages = [
            'email.required' => 'Поле "Email" обязательно для заполнения.',
            'email.email' => 'Поле "Email" должно быть в формате email.',
            'email.unique' => 'Такой email уже существует.',
            'password.required' => 'Поле "Пароль" обязательно для заполнения.',
            'password.string' => 'Поле "Пароль" должно быть строкой.',
            'password.min' => 'Поле "Пароль" должно содержать не менее :min символов.',
        ];

        
        try{
            $request->validate($rules, $messages);
        }catch(ValidationException $e){
            $errors = $e->validator->errors()->all();
            return response()->json(['message' => $errors], 422);
        }
        
        $user = User::where('email', $request['email'])->first();

       
        if (!$user || !Hash::check($request['password'], $user->password)){
            return response(['message'=> 'login or password incorrect'], 422);
        }
        
        $token = $user->createToken('secret1234')->plainTextToken;

        return response([
            'Success'=>true,
            'token'=> $token
        ], 200);
    }

    public function logout(Request $request){
        try{
            auth()->user()->tokens()->delete();
        }catch(\Throwable $e){
            return response(['Success'=> false, 422]);
        }


        return response(['message' => 'Logged out']);
    }
}
