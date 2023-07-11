<?php

namespace App\Http\Controllers;

use App\Models\AuthToken;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        return response()->json([
            'kind' => "success",
            'data' => [
                'details' => $user
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user && password_verify($request->password, $user->password)) {
            $newAuthToken = new AuthToken();
            $newAuthToken->user_id = $user->id;
            $newAuthToken->token = Str::uuid();
            $newAuthToken->save();

            return response()->json([
                'kind' => 'success',
                'data' => [
                    'token' => $newAuthToken->token,
                    'user' => $user
                ]
            ]);

            /* $token = $user->email;
            User::where('email', $user->email)->update(['api_token' => $token]); */
            //return response()->json(['token' => $token, 'user' => $user]);

        }

        return response()->json(['error' => 'Invalid user']);

    }
    public function alluser(Request $request)
    {
        $currentUser = User::where('id', $request->header('x-y-z-meow-id'))->first();
        return response()->json([
            'status' => 'success',
            'details' => [
                'bortomanUserId' => $request->header('x-y-z-meow-id'),
                'bortomanUserErAuthToken' => $request->header('x-y-z-meow-auth-token'),
                'bortomanUserDetails' => $currentUser
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $currentUser = User::where('id', $request->header('x-y-z-meow-id'))->first();
        $authToken = AuthToken::where('id', $currentUser->id)->where('token', $request->header('x-y-z-meow-auth-token'))->where('killed_at', null)->update(['killed_at' => Carbon::now()]);

        return response()->json([
            'status' => 'success',
            'details' => [
                // 'bortomanUserId' => $request->header('x-y-z-meow-id'),
                // 'bortomanUserErAuthToken' => $request->header('x-y-z-meow-auth-token'),
                'bortomanUserDetails' => $authToken
            ]
        ]);
    }
}