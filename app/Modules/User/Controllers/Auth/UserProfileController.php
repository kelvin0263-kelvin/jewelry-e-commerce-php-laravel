<?php

namespace App\Modules\User\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Modules\User\Facade\UserFacade;
use Carbon\Carbon;

class UserProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        return view('user::profile.show', compact('user'));
    }

    public function edit()
    {
        $user = Auth::user();
        return view('user::profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();
        $maxDate = $today->copy()->subYears(18);   // User must be at least 18 years old
        $minDate = $today->copy()->subYears(100);  // User cannot be older than 100 years

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'gender' => ['nullable', 'in:male,female'],
            'birthday' => [
                'nullable',
                'date',
                'after_or_equal:' . $minDate->toDateString(),  // at most 100 years old
                'before_or_equal:' . $maxDate->toDateString(), // at least 18 years old
            ],
        ], [
            'birthday.date' => 'Please enter a valid date.',
            'birthday.after_or_equal' => 'Birthday must indicate an age of 100 years or younger.',
            'birthday.before_or_equal' => 'You must be at least 18 years old.',
            'name.required' => 'Please enter your full name.',
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'Email cannot exceed 255 characters.',
            'gender.in' => 'Please select a valid gender (Male or Female).',
        ]);

        UserFacade::updateProfile($user, $validated);

        return back()->with('status', 'Profile information updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $updated = UserFacade::updatePassword($user, $validated['current_password'], $validated['password']);

        if (!$updated) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.'], 'updatePassword');
        }

        // 🔒 Invalidate other sessions
        $request->user()->tokens()->delete(); // Sanctum tokens
        Auth::logoutOtherDevices($validated['password']); // Laravel built-in

        return back()->with('status', 'Password changed successfully!');
    }

    public function destroy(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validateWithBag('deleteAccount', [
            'password' => ['required'],
        ]);

        if (!UserFacade::deleteUser($user, $validated['password'])) {
            return back()->withErrors(['password' => 'The password is incorrect.']);
        }

        return redirect('/')->with('status', 'Your account has been deleted.');
    }
}
