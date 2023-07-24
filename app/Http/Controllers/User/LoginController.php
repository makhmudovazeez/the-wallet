<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\LoginRequest;
use App\Http\Requests\User\RegistrationRequest;
use App\Http\Requests\VerifyMail;
use App\Http\Resources\User\ProfileResource;
use App\Mail\Mailing;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class LoginController extends Controller
{
    public function registration(RegistrationRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = User::create($request->validated());

            $code = random_int(100000, 999999);
            Cache::put($user->email, $code, 600);

            Mail::to($user->email)->send(new Mailing(
                [
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'code' => $code
                ]
            ));
            DB::commit();
            return new Response(new ProfileResource($user));
        } catch (\Exception $exception) {
            DB::rollBack();
            return new Response(['error' => $exception->getMessage()], ResponseAlias::HTTP_BAD_REQUEST);
        }
    }

    public function verifyMail(VerifyMail $request)
    {
        $code = Cache::get($request->post('email'), false);
        if (!$code) {
            return new Response([
                'error' => 'incorrect code'
            ],ResponseAlias::HTTP_BAD_REQUEST);
        }

        if ($request->post('code') !== $code) {
            return new Response([
                'error' => 'incorrect code'
            ],ResponseAlias::HTTP_BAD_REQUEST);
        }
        $user = User::where('email', $request->post('email'))->first();
        $user->update([
            'email_verified_at' => now(),
            'remember_token' => Str::random(10)
        ]);

        Cache::delete($request->post('email'));
        return new Response([
            'message' => 'success'
        ]);
    }

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
