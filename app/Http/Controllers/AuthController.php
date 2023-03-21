<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function register(Request $request) {
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:admins,email',
            'password' => 'required|string|confirmed',
            'isSuperAdmin' => 'nullable|boolean'
        ]);
        $image_path=$request->file('image')->store('public/images');;
         $user = Admin::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
            'isSuperAdmin' => $fields['isSuperAdmin'] ?? 0,
            'image' => $image_path,
          
        ]);

        $token = $user->createToken('Batatatoken')->plainTextToken;
        
        $response = [
            'user' => $user,
            'token' => $token
        ];
        return response()->json(['message' =>$response], 201);
    }

    public function login(Request $request) {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);
        $user = Admin::where('email', $fields['email'])->first();
        
        if(!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'Bad creds'
            ], 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;
        
        $image = file_get_contents(storage_path('app/' . $user->image));
        $encodedImage = base64_encode($image);
        unset($user->image_path);
        $user->image = $encodedImage;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

   /* public function logout(Request $request) {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Logged out'
        ];
    }*/
}

?>