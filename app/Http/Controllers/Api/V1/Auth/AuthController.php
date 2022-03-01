<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Transformers\Auth\LoginTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;

class AuthController extends Controller
{
    public function actionMe(Request $request)
    {
        return $request->user()->with(['profile'])
            ->first();
    }

    public function actionLogin(Request $request)
    {
        // Login request validation
        if (filter_var($request->input('identity'), FILTER_VALIDATE_EMAIL)) {
            $identity = [
                'identity' => 'required|email'
            ];
        } else {
            $identity = [
                'identity' => 'required'
            ];
        }

        $validatedLogin = Validator::make($request->all(), $identity);

        if ($validatedLogin->fails()) {
            return $this->responseUnprocessable($validatedLogin->errors());
        }

        // Check identity which used to login
        if (filter_var($request->input('identity'), FILTER_VALIDATE_EMAIL)) {
            $credential = [
                'email' => $request->input('identity'),
                'password' => $request->input('password'),
                'status' => 'enable'
            ];
        } else {
            $credential = [
                'username' => $request->input('identity'),
                'password' => $request->input('password'),
                'status' => 'enable'
            ];
        }

        // Attemp to login
        if (!Auth::attempt($credential)) {
            return $this->responseUnprocessable(['Invalid credential']);
        }

        // Provide access token
        $user = Auth::user();

        if(!$user->email_verified_at) {
            return $this->responseUnprocessable(['Please verify your email']);
        }

        $token = $user->createToken('token')->accessToken;

//        return fractal($user, new LoginTransformer)
//            ->addMeta(['token' => $token])
//            ->includeProfile()
//            ->includeRole()
//            ->toArray();

        $cookie = cookie('token', $token, 60 * 24);
        return response(['message' => 'Success'], 200)
            ->withCookie($cookie);
    }

    public function actionLogout(Request $request)
    {
        // Get user by email
        $user = User::find(Auth::user()->id);

        if (!$user)
            return $this->responseServerError(new MessageBag(['Logout failed']));

        // Remove the access token
        $oAuthAccessTokens = $user->oAuthAccessTokens();
        $oAuthAccessTokens->delete();

        $request->user()->token()->revoke();

//        return $this->responseSuccess('Logout succeed');

        $cookie = Cookie::forget('token');
        return response(['message' => 'Success'], 200)->withCookie($cookie);
    }
}
