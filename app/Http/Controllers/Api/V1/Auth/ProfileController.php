<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Resources\ProfileResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        return ProfileResource::make($request->user());
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($request->user()->id)]
        ]);

        $request->user()->update($request->only('name', 'email'));
        
        return ProfileResource::make($request->user());
    }
}
