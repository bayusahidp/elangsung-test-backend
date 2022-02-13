<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\JWTAuth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'loginadmin']]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:6',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'login gagal, username harus diisi dan password minimal 6 karakter',
                'data' => null,
                'error' => $validator->errors()->toJson(),
            ], 200);
        }

        if(!$token=auth()->attempt($validator->validate())){
            return response()->json([
                'status' => false,
                'message' => 'username atau password anda salah',
                'data' => null,
                'error' => null,
            ], 200);
        }

        return $this->createNewToken($token);
    }

    public function loginadmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:6',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'login gagal, username harus diisi dan password minimal 6 karakter',
                'data' => null,
                'error' => $validator->errors()->toJson(),
            ], 200);
        }

        if(!$token=auth()->attempt($validator->validate())){
            return response()->json([
                'status' => false,
                'message' => 'username atau password anda salah',
                'data' => null,
                'error' => null,
            ], 200);
        }

        if (auth()->user()->role != '1') {
            return response()->json([
                'status' => false,
                'message' => 'anda tidak memiliki akses',
                'data' => null,
                'error' => null,
            ], 200);
        }

        return $this->createNewToken($token);
    }

    public function createNewToken($token)
    {
        return response()->json([
            'status' => true,
            'message' => 'login berhasil',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => auth()->user(),
            ],
            'error' => null,
        ], 200);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:6',
            'fakultas' => 'required|string|max:255',
            'jurusan' => 'required|string|max:255',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'registrasi gagal',
                'data' => null,
                'error' => $validator->errors()->toJson()
            ], 200);
        }

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'username' => $request->get('username'),
            'password' => Hash::make($request->get('password')),
            'fakultas' => $request->get('fakultas'),
            'jurusan' => $request->get('jurusan'),
            'role' => '0'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'register berhasil',
            'data' => [
                'user' => $user,
            ],
            'error' => null,
        ], 200);
    }

    public function logout()
    {
        auth()->logout();

        return response()->json([
            'status' => true,
            'message' => 'logout berhasil',
            'data' => null,
            'error' => null,
        ], 200);
    }

    public function getcsrftoken() {
        return response()->json([
            'status' => true,
            'message' => 'get token',
            'data' => csrf_token(),
            'error' => null,
        ], 200);
    }
}
