<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UpdateUserPasswordRequest;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return UserResource::collection(User::all());
    }

    public function show(User $user)
    {
        if ($user->isCustomer())
        UserResource::$format = 'withCustomer';

        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();

        $user->update($validated);

        if ($user->isCustomer()) {
            $user->customer->update($validated['customer']);
            UserResource::$format = 'withCustomer';
        }

        return new UserResource($user);
    }

    public function update_password(Request $request, User $user)
    {
        $validated = $request->validate([
            'current_password' => ['required', function ($attribute, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    return $fail(__('The current password is incorrect.'));
                }
            }],
            'password' => ['required', 'different:current_password'],
            'password_confirmation' => ['same:password'],
        ]);

        $user->password = Hash::make($validated['password']);
        $user->save();

        return new UserResource($user);
    }

    public function show_me(Request $request)
    {
        $user = $request->user();
        $user->name = explode(" ", $user->name)[0] . " " . explode(" ", $user->name)[substr_count($user->name, " ")];

        return new UserResource($request->user());
    }
}
