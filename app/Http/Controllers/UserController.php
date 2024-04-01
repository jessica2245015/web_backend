<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserLoginRequest;

use App\http\Resources\UserResource;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\http\JsonResponse;

use Illuminate\support\Str;
use Illuminate\support\Facades\Hash;

use Illuminate\Http\Exceptions\HttpResponseException;

class UserController extends Controller
{
    public function register (UserRegisterRequest $request): JsonResponse
    {
        $user = User::Create($request->validated());

        return (new UserResource($user))->response()->setStatusCode(201);
    }

    public function login(UserLoginRequest $request): UserResource
    {
        $data = $request->validated();
        $user = User::where('email', $data['email'])->first();
        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new HttpresponseException (response([
                'errors' => [
                    'message' => ['username or password wrong'],
                ]
            ],401));
        }
        
        $user->remember_token = str::uuid()->toString();
        $user->save();
        return new UserResource($user);
    }
}
