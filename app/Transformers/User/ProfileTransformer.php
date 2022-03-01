<?php

namespace App\Transformers\User;

use App\Models\Profile;
use League\Fractal\TransformerAbstract;

class ProfileTransformer extends TransformerAbstract
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
        //
    ];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Profile $profile)
    {
        return [
            'identity_number' => $profile->identity_number,
            'full_name' => $profile->full_name,
            'nick_name' => $profile->nick_name,
            'gender' => $profile->gender,
            'nationality' => $profile->nationality,
            'address' => $profile->address,
            'post_code' => $profile->post_code,
            'phone' => $profile->phone
         ];
    }
}
