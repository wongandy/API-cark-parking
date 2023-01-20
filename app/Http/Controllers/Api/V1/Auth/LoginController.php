<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $request->email)->first();
   
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Wrong credentials'], Response::HTTP_UNAUTHORIZED);
        }
        
        return UserResource::make($user);
    }
}
