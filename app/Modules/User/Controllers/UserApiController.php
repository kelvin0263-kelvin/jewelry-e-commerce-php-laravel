<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Modules\User\Facade\UserFacade;

class UserApiController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = UserFacade::register($request->only('name', 'email', 'password'));
        $token = $user->createToken('user-api')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');
        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 422);
        }

        $user = Auth::user();
        $token = $user->createToken('user-api')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return response()->json(['message' => 'Logged out']);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $request->user()->id,
            'gender' => 'sometimes|in:male,female,other',
            'birthday' => 'sometimes|date',
        ]);

        $updated = UserFacade::updateProfile($request->user(), $request->only('name', 'email', 'gender', 'birthday'));
        return $updated
            ? response()->json(['message' => 'Profile updated'])
            : response()->json(['message' => 'No changes made'], 200);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $success = UserFacade::updatePassword($request->user(), $request->input('current_password'), $request->input('password'));
        if (!$success) {
            return response()->json(['message' => 'Current password is incorrect'], 422);
        }

        return response()->json(['message' => 'Password updated']);
    }

    public function deleteAccount(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $deleted = UserFacade::deleteUser($request->user(), $request->input('password'));
        if (!$deleted) {
            return response()->json(['message' => 'Password is incorrect'], 422);
        }

        return response()->json(['message' => 'Account deleted']);
    }

    public function requestResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = UserFacade::findUserByEmail($request->email);
        if (!$user) {
            // Do not reveal existence
            return response()->json(['message' => 'If the email exists, a code has been issued']);
        }

        $code = UserFacade::sendResetCode($user);
        return response()->json(['message' => 'If the email exists, a code has been issued']);
    }

    public function verifyResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|digits:6',
        ]);

        $ok = UserFacade::verifyResetCode($request->email, (int) $request->code);
        return $ok
            ? response()->json(['message' => 'Code verified'])
            : response()->json(['message' => 'Invalid code'], 422);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $ok = UserFacade::resetPassword($request->email, $request->input('password'));
        return $ok
            ? response()->json(['message' => 'Password reset successful'])
            : response()->json(['message' => 'Reset failed'], 422);
    }
}
