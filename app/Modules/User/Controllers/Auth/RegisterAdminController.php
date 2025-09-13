<?php

namespace App\Modules\User\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Modules\User\Models\User;
use App\Modules\User\Facade\UserFacade;

class RegisterAdminController extends Controller
{
    /**
     * Show the admin registration form.
     */
    public function createAdmin()
    {
        return view('user::auth.admin-register');
    }

    /**
     * Handle the admin registration request.
     */
    public function storeAdmin(Request $request)
    {
        // Validate input including admin credentials
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'admin_email' => 'required|email|exists:users,email',
            'admin_password' => 'required|string',
        ], [
            'admin_email.exists' => 'The admin email does not exist in the system.',
        ]);

        // Find admin user from DB
        $admin = User::where('email', $request->admin_email)
            ->where('is_admin', 1)
            ->first();

        // If not found OR password doesn't match
        if (!$admin) {
            return back()->withErrors([
                'admin_email' => 'The provided email is not an admin account.',
            ])->withInput();
        }

        if (!Hash::check($request->admin_password, $admin->password)) {
            return back()->withErrors([
                'admin_password' => 'The admin password is incorrect.',
            ])->withInput();
        }

        // Create new user as admin
        $userData = $request->only(['name', 'email', 'password']);
        $user = UserFacade::registerAsAdmin($userData);

        Auth::login($user);

        return redirect()->route('dashboard')->with('status', 'Admin registration successful!');
    }
}
