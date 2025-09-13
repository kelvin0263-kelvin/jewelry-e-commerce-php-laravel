<?php

namespace App\Modules\User\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\User\Facade\UserFacade;

class NewPasswordController extends Controller
{
    public function create(Request $request)
    {
        return view('user::auth.reset-password', [
            'email' => $request->email ?? session('reset_email')
        ]);
    }

    public function customUpdate(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        UserFacade::resetPassword($request->email, $request->password);

        return redirect()->route('login')->with('status', 'Password reset successful. You can now login.');
    }
}
