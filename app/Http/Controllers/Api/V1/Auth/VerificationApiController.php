<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;

class VerificationApiController extends Controller
{
    use VerifiesEmails;

    public function show()
    {
    }

    public function verify(Request $request) {
        $userID = $request["id"];

        $user = User::findOrFail($userID);

        $date = date("Y-m-d g:i:s");
        $user->email_verified_at = $date;

        $user->save();

        return response()->json("Email verified!");
    }

    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json("User already have verified email!", 422);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json("The notification has been resubmitted");
    }
}
