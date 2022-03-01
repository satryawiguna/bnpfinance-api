<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function view(User $user)
    {
        return $user->role_id == 1;
    }

    public function create(User $user)
    {
        return $user->role_id == 1;
    }

    public function update(User $user)
    {
        return $user->role_id == 1;
    }

    public function delete(User $user)
    {
        return $user->role_id == 1;
    }
}
