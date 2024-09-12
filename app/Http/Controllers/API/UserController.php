<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class UserController extends BaseController
{
    
        public function photo_profile(Request $request, $id){
            Log::info('store method reached');
            $validator = Validator::make($request->all(), [
                'photo_profile' => 'required'
            ]);

            $image = $request->file('photo_profile');
            $imgName = time() . rand();
            if(!file_exists(public_path('img/' . $image->getClientOriginalName()))){
                $destinationPath = public_path('img/');
                $image->move($destinationPath, $imgName);
                $uploaded = $imgName;
            }else{
                $uploaded = $image->getClientOriginalName();
            }

            if ($validator->fails()) {
                Log::info('Validation failed', $validator->errors()->toArray());
                return response()->json($validator->errors(), 422);
            }

            try {
                $user = User::where('id', $id)->update([
                    'photo_profile' => $uploaded,
                ]);

                return response()->json([
                    'message' => 'Registered successfully.',
                    'user' => $user,
                ], 201);
    
        }
        catch (\Exception $e) {
            Log::error('Error update photo profile', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'update photo profile failed'], 500);
        }
    }

    public function detail_profile_update(Request $request, $id){
        Log::info('Register method reached');

        $validator = Validator::make($request->all(), [
            'description' => 'required|max:200',
            'caption' => 'required|max:100'
        ]);
        if ($validator->fails()) {
            Log::info('Validation failed', $validator->errors()->toArray());
            return response()->json($validator->errors(), 422);
        }


        try {
            $user = User::where('id', $id)->update([
                'description' => $request->description,
                'caption' => $request->caption
            ]);
            return response()->json([
                'message' => 'house created successfully',
                'user' => $user
            ]);  
        }catch (\Exception $e) {
            Log::error('Error in update profile', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'update  failed'], 500); 
        }
    }

   

    public function store(Request $request)
    {
        Log::info('store method reached');

        $validator = Validator::make($request->all(), [
            'username' => 'required|min:3|unique:users,username',
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'role' => 'required'
        ]);

        $defaultImage = 'img/default_profile.jpeg'; 
        $uploaded = $defaultImage;

        if ($validator->fails()) {
            Log::info('Validation failed', $validator->errors()->toArray());
            return response()->json($validator->errors(), 422);
        }
        try {
            $user = User::create([
                'username' => $request->username,
                'name' => $request->name,
                'email' => $request->email,
                'photo_profile' => $uploaded,
                'role' => $request->role,
                'password' => bcrypt($request->password),
            ]);

            Log::info('User created', $user->toArray());
            $token = $user->createToken('RandomKeyPassportAuth')->accessToken;

            Log::info('Token created', ['token' => $token]);
            return response()->json([
                'message' => 'Registered successfully.',
                'token' => $token,
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error in registration', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Registration failed'], 500);
        }
    }

    public function delete($id){
        $user = User::find($id)->delete();
        return response()->json([
            'message' => 'Registration deleted',
            'user' => $user
        ]);
    }


}
