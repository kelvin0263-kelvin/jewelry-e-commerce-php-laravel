<?php

namespace App\Modules\User\Services;

use App\Modules\User\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class UserService
{
    /**
     * Register a new user.
     */
    public function register(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    /**
     * Register a new user as admin.
     */
    public function registerAsAdmin(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_admin' => 1,
        ]);
    }

    /**
     * Update user's profile information.
     */
    public function updateProfile(User $user, array $data): bool
    {
        return $user->update([
            'name' => $data['name'] ?? $user->name,
            'email' => $data['email'] ?? $user->email,
            'gender' => $data['gender'] ?? $user->gender,
            'birthday' => $data['birthday'] ?? $user->birthday,
        ]);
    }

    /**
     * Update user's password.
     */
    public function updatePassword(User $user, string $currentPassword, string $newPassword): bool
    {
        if (!Hash::check($currentPassword, $user->password)) {
            return false;
        }

        $user->update(['password' => Hash::make($newPassword)]);
        return true;
    }

    /**
     * Delete user account after confirming password.
     */
    public function deleteUser(User $user, string $password): bool
    {
        if (!Hash::check($password, $user->password)) {
            return false;
        }

        $user->delete();
        return true;
    }

    /**
     * Find a user by email.
     */
    public function findUserByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Generate and store a 6-digit password reset code.
     */
    public function sendResetCode(User $user): int
    {
        $code = rand(100000, 999999);
        Session::put('reset_code', $code);
        Session::put('reset_email', $user->email);
        return $code;
    }

    /**
     * Verify the password reset code.
     */
    public function verifyResetCode(string $email, int $code): bool
    {
        $storedCode = Session::get('reset_code');
        $storedEmail = Session::get('reset_email');

        return $email === $storedEmail && $code === (int)$storedCode;
    }

    /**
     * Reset password using email.
     */
    public function resetPassword(string $email, string $newPassword): bool
    {
        $user = $this->findUserByEmail($email);
        if (!$user) {
            return false;
        }

        $user->update(['password' => Hash::make($newPassword)]);
        Session::forget(['reset_code', 'reset_email']);
        return true;
    }

    /**
     * Verify email code for email verification.
     */
    public function verifyEmailCode(string $email, int $code): bool
    {
        return $this->verifyResetCode($email, $code);
    }

    
}
