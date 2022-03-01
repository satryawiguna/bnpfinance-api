<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\User;
use App\Transformers\User\ProfileTransformer;
use App\Transformers\User\UserTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function actionProfile()
    {
        $user = (new User())->with(['profile'])->find(Auth::user()->id);

        // Get profile
        $profile = $user->profile;

        // Check if user is exists
        if (!$profile)
            return $this->responseUnprocessable(new MessageBag(['Profile is not found']));

        return fractal($profile, new ProfileTransformer)->toArray();
    }

    public function actionProfileUpdate(Request $request)
    {
        $user = (new User())->with(['profile'])->find(Auth::user()->id);

        // Get profile
        $profile = $user->profile;

        // Check if user is exists
        if (!$profile)
            return $this->responseUnprocessable(new MessageBag(['Profile is not found']));

        // Profile request validation
        $validatedProfile = Validator::make($request->all(), [
            'identity_number' => 'required|string',
            'full_name' => 'required|string',
            'nick_name' => 'required|string'
        ]);

        if ($validatedProfile->fails()) {
            return $this->responseUnprocessable($validatedProfile->errors());
        }

        // Update profile
        $profile->update([
            'identity_number' => $request->input('identity_number'),
            'nick_name' => $request->input('nick_name'),
            'full_name' => $request->input('full_name'),
            'gender' => $request->input('gender') ?: null,
            'nationality' => $request->input('nationality') ?: null,
            'address' => $request->input('address') ?: null,
            'post_code' => $request->input('post_code') ?: null,
            'phone' => $request->input('phone') ?: null
        ]);

        return fractal($profile, new ProfileTransformer())->toArray();
    }

    public function actionPhotoUpdate(Request $request)
    {
        // Get profile
        $profile = (new User())->with(['contact'])
            ->find($request->input('id'));

        // Check if user is exists
        if (!$profile)
            return $this->responseUnprocessable(new MessageBag(['Profile is not found']));

        // Photo request validation
        $validatedPhoto = Validator::make($request->all(), [
            'photo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if ($validatedPhoto->fails()) {
            return $this->responseUnprocessable($validatedPhoto->errors());
        }

        if ($request->file('photo')) {
            // Check if any photo existing it will delete first
            if ($profile->contact->photo && File::exists(storage_path('app/profile/' . basename($profile->contact->photo))))
                File::delete(storage_path('app/profile/' . basename($profile->contact->photo)));

            // Upload photo
            $name = time() . '.' . $request->file('photo')->extension();
            $request->file('photo')->storeAs('profile', $name);

            // Update photo in database
            $profile->contact->update([
                'photo' => asset('photo/profile') . '/' . $name
            ]);
        } else {
            // Update photo in database
            $profile->contact->update([
                'photo' => null
            ]);
        }

        return response()->json([
            'photo' => $profile->contact->photo
        ]);
    }

    public function actionPasswordUpdate(Request $request)
    {
        $user = (new User())->find(Auth::user()->id);

        // Check if user is exists
        if (!$user)
            return $this->responseUnprocessable(new MessageBag(['User is not found']));

        // Profile request validation
        $validatedUser = Validator::make($request->all(), [
            'password' => ['required', 'confirmed', Password::min(8)]
        ]);

        if ($validatedUser->fails()) {
            return $this->responseUnprocessable($validatedUser->errors());
        }

        // Update Password
        $user->update([
            'password' => Hash::make($request->input('password'))
        ]);

        return fractal($user, new UserTransformer())->toArray();
    }
}
