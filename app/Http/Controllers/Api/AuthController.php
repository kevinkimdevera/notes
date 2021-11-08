<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;

class AuthController extends Controller
{
  public function registerAccount (RegisterRequest $request) {
    $user = User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => Hash::make($request->password)
    ]);

    if (!!$user) {
      return [
        'created' => !!$user,
        'token' => $user->createToken('notes-app-token')->plainTextToken
      ];
    }

    return [
      'created' => false
    ];
  }

  public function attemptLogin (LoginRequest $request) {
    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
      return response([
        'message' => 'These credentials do not match our records.'
      ], 404);
    }

    return response([
      'token' => $user->createToken('notes-app-token')->plainTextToken
    ], 201);
  }

  public function showUser (Request $request) {
    return new UserResource($request->user());
  }
}
