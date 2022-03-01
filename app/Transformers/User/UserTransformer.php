<?php

namespace App\Transformers\User;

use App\Models\User;
use App\Transformers\Common\RoleTransformer;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [

    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'profile', 'role'
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(User $user)
    {
        return [
            'id' => $user->id,
            'email' => $user->email
        ];
    }

    public function includeProfile(User $user)
    {
        $profile = $user->profile;

        return $this->item($profile, new ProfileTransformer);
    }

    public function includeRole(User $user)
    {
        $role = $user->role;

        return $this->item($role, new RoleTransformer);
    }
}
