<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Order;
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

    public function view(User $user, Order $model)
    {
        return $user->isEmployee() || $model->customer_id && ($user->id == $model->customer->user_id);
    }

    public function create(User $user)
    {
        return $user->isManager();
    }

    public function update(User $user, Order $model)
    {
        return $user->isManager() || $user->id == $model->delivered_by;
    }

    public function delete(User $user, Order $model)
    {
        return $user->isManager() || $user->id == $model->id;
    }
}
