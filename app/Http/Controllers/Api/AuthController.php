<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;

class AuthController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $input = $request->all();
        //set validation
        $validator = Validator::make($input, [
            'name'      => 'required',
            'email'     => 'required|email|unique:users',
            'password'  => 'required'
        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //create user
        $user = User::create([
            'name'      => $input['name'],
            'email'     => $input['email'],
            'password'  => bcrypt($input['password']),
            'role_id'   => '2',
        ]);

        //return response JSON user is created
        if($user) {
            return response()->json([
                'success' => true,
                'message' => 'Registrasi Berhasil!',
                'user'    => $user,  
            ], 200);
        }

        //return JSON process insert failed 
        return response()->json([
            'success' => false,
            'message' => 'Registrasi Gagal!'
        ], 409);
    }

    public function getUser($id){
        $use = User::find($id);
        return response()->json($use);
    }

    public function updateUser($id, Request $request){
        $usr = User::where('id',$id)->first();
        $usr->name = $request->name;
        $usr->email = $request->email;
        $usr->save();
        //return response JSON user is created
        return response()->json([
            'success' => true,
            'user'    => $usr,
            'status' => 200,
            'message' => 'Successfully Edit Data' 
        ], 200);
    }

    public function show(){
        $input = User::all();

        return response()->json([
            'user'    => $input,
            'message' => 'User',
            'code' => 200
        ]);
    }

    public function login(Request $request)
    {
        //set validation
        $validator = Validator::make($request->all(), [
            'email'     => 'required',
            'password'  => 'required'
        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Mendapatkan kredensial dari request
        $credentials = $request->only('email', 'password');

        try {
            // Mencoba untuk melakukan otentikasi
            if (!$token = auth()->guard('api')->attempt($credentials)) {
                // Jika otentikasi gagal
                return response()->json([
                    'success' => false,
                    'message' => 'Email Atau Password Anda Salah!'
                ], 401);
            }
        } catch (JWTException $e) {
            // Jika pembuatan token gagal
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat token.'
            ], 500);
        }

        // Jika otentikasi berhasil, mengambil pengguna yang diautentikasi
        $user = auth()->guard('api')->user();
        // Membuat kata sambutan
        $sambutan = 'Selamat Datang, ' . $user->name . '!';
        $role = $user->role_id;

        if ($role === 1) {
            // Jika role_id adalah 1
            return response()->json([
                'success' => true,
                'message' => $sambutan,
                'role'    => $role,
                'token'   => $token,
            ], 200);
        } elseif ($role === 2) {
            // Jika role_id adalah 2
            return response()->json([
                'success' => true,
                'message' => $sambutan,
                'role'    => $role,
                'token'   => $token,
            ], 200);
        }
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {        
        // Menghapus token
        try {
            $removeToken = JWTAuth::invalidate(JWTAuth::getToken());

            if ($removeToken) {
                // Mengembalikan respons JSON sukses
                return response()->json([
                    'success' => true,
                    'message' => 'Logout Berhasil!',  
                ]);
            }
        } catch (JWTException $e) {
            // Jika proses logout gagal
            return response()->json([
                'success' => false,
                'message' => 'Gagal logout.'
            ], 500);
        }
    }

    public function deleteUser($id){
        $usr = User::find($id);
        if($usr){
            $usr->delete();
            return response()->json([
                'message' => "Data successfully deleted",
                'code' => 200
            ]);
        }else{
            return response([
                'message' => "Failed delete data $id / data doesn't exists"
            ]);
        }
    }
}