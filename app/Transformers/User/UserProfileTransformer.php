<?php

namespace App\Transformers\User;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserProfileTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'profile'
    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        //
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(User $user)
    {
        return [
            'email' => $user->email
        ];
    }

    public function includeProfile(User $user)
    {
        $profile = $user->profile;

        return $this->item($profile, new ProfileTransformer());
    }
}
