<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{

    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if(!Hash::check($request->password, $user->password)){
            return 'Connot login';
        }

        $token = $user->createToken($user->name);
        return response()->json(['token' => $token->plainTextToken , 'user'=> $user ]);
    }

}
