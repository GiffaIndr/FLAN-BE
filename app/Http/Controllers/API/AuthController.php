<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;
class AuthController extends BaseController
{
 
    public function register(Request $request)
    {
        Log::info('Metode register dicapai');
    
        $validator = Validator::make($request->all(), [
            'username' => 'required|min:3|unique:users,username',
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ]);
    
        $defaultImage = 'img/default_profile.jpeg'; 
        $uploaded = $defaultImage;
    
        if ($validator->fails()) {
            Log::info('Validasi gagal', $validator->errors()->toArray());
            return response()->json($validator->errors(), 422);
        }
    
        try {
            Log::info('Membuat user dengan data', [
                'username' => $request->username,
                'name' => $request->name,
                'email' => $request->email,
                'photo_profile' => $uploaded,
                'password' => bcrypt($request->password),
            ]);
    
            $user = User::create([
                'username' => $request->username,
                'name' => $request->name,
                'email' => $request->email,
                'photo_profile' => $uploaded,
                'password' => bcrypt($request->password),
            ]);
    
            Log::info('User berhasil dibuat', $user->toArray());
            $token = $user->createToken('RandomKeyPassportAuth')->accessToken;
    
            Log::info('Token berhasil dibuat', ['token' => $token]);
            return response()->json([
                'message' => 'Registrasi berhasil.',
                'token' => $token,
            ], 201);
    
        } catch (\Exception $e) {
            Log::error('Error saat registrasi', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Registrasi gagal'], 500);
        }
    }
    
    /**
     * Login Req
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
    
        $credentials = $request->only('email', 'password');
    
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('RandomKeyPassportAuth')->accessToken;
            return $this->sendResponse(['token' => $token], 'Login successful.');
        } else {
            return $this->sendError('Unauthorised', ['error' => 'Unauthorised'], 401);
        }
    }

    //login googlefdgz
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
{
    try {
        $googleUser = Socialite::driver('google')->user();
        Log::info('Google User Info:', ['googleUser' => $googleUser]);

        // Cek apakah pengguna sudah terdaftar berdasarkan email
        $user = User::where('email', $googleUser->email)->first();

        // Jika pengguna sudah terdaftar, langsung lakukan login
        if ($user) {
            Auth::login($user);
            $token = $user->createToken('RandomKeyPassportAuth')->accessToken;
            return response()->json([
                'message' => 'Login successful.',
                'token' => $token,
            ], 200);
        } else {
            // Jika pengguna belum terdaftar, buat akun baru dengan informasi dari Google
            $username = $googleUser->name; // Menggunakan nama Google sebagai username
            $name = $googleUser->name;
            $email = $googleUser->email;
            $avatar = $googleUser->avatar; // Menggunakan avatar Google sebagai photo_profile

            // Simpan gambar avatar ke direktori
            $defaultImage = 'img/default_profile.jpeg';
            $uploaded = $defaultImage;

            // Buat pengguna baru dalam database
            $user = User::create([
                'username' => $username,
                'name' => $name,
                'email' => $email,
                'photo_profile' => $avatar, // Gunakan avatar Google sebagai photo_profile
                'password' => bcrypt('defaultpassword'), // Anda dapat mengatur password default atau metode lain
            ]);

            // Lakukan login setelah berhasil membuat pengguna baru
            Auth::login($user);
            $token = $user->createToken('RandomKeyPassportAuth')->accessToken;

            return response()->json([
                'message' => 'Registered and logged in successfully.',
                'token' => $token,
            ], 201);
        }

    } catch (   \Exception $e) {
        Log::error('Error in Google callback', ['error' => $e->getMessage()]);
        return response()->json(['error' => 'Authentication failed'], 500);
    }
}

    
    public function logout(){
         Auth::logout();
        return response()->json([
            'message' => "success logout"
        ], 200);

    }

    public function user(){
        $users = User::all();
        return response()->json([
            'message' => "method success",
            'user' => $users,
        ], 200);
    }
 
    public function userInfo()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
    
        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user
            ],
            'message' => 'User retrieved successfully.'
        ], 200);
    }
}
