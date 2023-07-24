<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\LoginRequest;
use App\Http\Resources\User\ProfileResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = [];
        if ($request->has('username')) {
            $credentials['username'] = $request->post('username');
        } else {
            $credentials['email'] = $request->post('email');
        }

        if (Auth::attempt($credentials)) {
            return new Response([
                'token' => \auth()->user()->createToken('user-login')->plainTextToken
            ]);
        }
        return new Response([], ResponseAlias::HTTP_UNAUTHORIZED);
    }

    public function profile()
    {
        return new Response(new ProfileResource(\auth()->user()));
    }
}
