<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
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

    public function delete(User $user, User $model)
    {
        return $user->isManager() || $user->id == $model->id;
    }
}
