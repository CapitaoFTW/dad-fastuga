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
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        return UserResource::collection(User::all());
    }

    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();
        $validated['password'] = bcrypt($validated['password']);
        $newUser = User::create($validated);

        if (array_key_exists('photo', $validated)) {
            $image_64 = $validated['photo'];
            $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];

            if ($extension == 'jpeg')
                $extension = 'jpg';

            $replace = substr($image_64, 0, strpos($image_64, ',') + 1);
            $image = str_replace($replace, '', $image_64);
            $image = str_replace(' ', '+', $image);
            $imageName = Str::random(10) . '.' . $extension;

            Storage::put('public/fotos/' . $imageName, base64_decode($image));

            $newUser->photo_url = $imageName;
            $newUser->save();
        }

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

        $user->update($validated);

        if (array_key_exists('photo', $validated)) {
            Storage::delete('storage/fotos/' . $user->photo_url);
            $image_64 = $validated['photo'];
            $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];

            if ($extension == 'jpeg')
                $extension = 'jpg';

            $replace = substr($image_64, 0, strpos($image_64, ',') + 1);
            $image = str_replace($replace, '', $image_64);
            $image = str_replace(' ', '+', $image);
            $imageName = Str::random(10) . '.' . $extension;

            Storage::put('public/fotos/' . $imageName, base64_decode($image));

            $user->photo_url = $imageName;
            $user->save();
        }

        if ($user->isCustomer()) {
            if (array_key_exists('customer', $validated)) {
                Customer::where('user_id', $user->id)->update($validated['customer']);
                UserResource::$format = 'withCustomer';
            }
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
