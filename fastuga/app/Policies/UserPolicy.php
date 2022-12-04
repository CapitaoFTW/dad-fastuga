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

    public function viewAny(User $user)
    {
        return $user->isManager();
    }

    public function view(User $user, User $model)
    {
        return $user->isManager() || $user->id == $model->id;
    }

    public function create(User $user)
    {
        return $user->isManager();
    }

    public function update(User $user, User $model)
    {
        return $user->isManager() || $user->id == $model->id;
    }

    public function updatePassword(User $user, User $model)
    {
        return $user->id == $model->id;
    }

    public function updateBlocked(User $user)
    {
        return $user->isManager();
    }

    public function delete(User $user, User $model)
    {
        return $user->isManager() || $user->id == $model->id;
    }
}
