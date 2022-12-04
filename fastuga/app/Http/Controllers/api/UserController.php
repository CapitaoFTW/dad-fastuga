<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Customer;
use App\Http\Resources\UserResource;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UpdateBlockUserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index()
    {
        return UserResource::collection(User::all());
    }

    public function store(StoreUserRequest $request)
    {
        $newUser = User::create($request->validated());
        return new UserResource($newUser);
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

        if ($request->hasFile('photo_url')) {
            Storage::delete('storage/fotos/' . $user->photo_url);
            $path = $request->photo_url->store('storage/fotos');
            $validated['photo_url'] = basename($path);
        }

        $user->update($validated);

        if ($user->isCustomer()) {
            $user->customer()->update($validated['customer']);
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

    public function update_blocked(UpdateBlockUserRequest $request, User $user)
    {
        $user->blocked = $request->validated()['blocked'];
        $user->save();

        return new UserResource($user);
    }

    public function show_me(Request $request)
    {
        $user = $request->user();
        $user->name = explode(" ", $user->name)[0] . " " . explode(" ", $user->name)[substr_count($user->name, " ")];

        if ($user->isCustomer()) {
            $user->customer = $request['customer'];
            UserResource::$format = 'withCustomer';
        }

        if ($user->isBlocked())
            return response()->json('User is blocked!', 403);

        return new UserResource($request->user());
    }

    public function destroy(User $user)
    {
        if ($user->isCustomer()) {
            Customer::where('user_id', $user->id)->delete();

            UserResource::$format = 'withCustomer';
        }


        $user->delete();
        return new UserResource($user);
    }
}
