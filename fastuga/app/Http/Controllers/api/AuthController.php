<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;

const PASSPORT_SERVER_URL = "http://fastuga.test";
const CLIENT_ID = 2;
const CLIENT_SECRET = 'arrozdoce2420';

class AuthController extends Controller
{
    private function passportAuthenticationData($email, $password)
    {
        return [
            'grant_type' => 'password',
            'client_id' => CLIENT_ID,
            'client_secret' => CLIENT_SECRET,
            'username' => $email,
            'password' => $password,
            'scope' => ''
        ];
    }

    public function login(Request $request)
    {
        try {
            request()->request->add($this->passportAuthenticationData($request->email, $request->password));
            $request = Request::create(PASSPORT_SERVER_URL . '/oauth/token', 'POST');
            $response = Route::dispatch($request);
            $errorCode = $response->getStatusCode();
            $auth_server_response = json_decode((string) $response->content(), true);

            return response()->json($auth_server_response, $errorCode);

        } catch (\Exception $e) {
            return response()->json('Authentication has failed!', 401);
        }
    }

    public function logout(Request $request)
    {
        $accessToken = $request->user()->token();
        $token = $request->user()->tokens->find($accessToken);
        $token->revoke();
        $token->delete();
        return response(['msg' => 'Token revoked'], 200);
    }

    public function register(RegisterCustomerRequest $request)
    {
        $validated = $request->validated();

        $validated['password'] = bcrypt($validated['password']);

        $user = User::create($validated);

        Customer::create([
            'user_id' => $user->id,
            'phone' => $validated['phone'],
            'points' => 0,
            'nif' => $validated['nif'],
            'default_payment_type' => $validated['payment_type'],
            'default_payment_reference' => $validated['payment_reference'],
        ]);

        return $this->login($request);
    }
}
