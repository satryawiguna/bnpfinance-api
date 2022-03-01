<?php

namespace App\Transformers\Auth;

use App\Models\User;
use App\Transformers\User\ProfileTransformer;
use League\Fractal\TransformerAbstract;

class RegisterTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        //
    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'profile'
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(User $user)
    {
        return [
            'username' => $user->username,
            'email' => $user->email,
        ];
    }

    public function includeProfile(User $user)
    {
        $profile = $user->profile;

        return $this->item($profile, new ProfileTransformer());
    }
}
