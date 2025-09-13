<?php

namespace App\Modules\User\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\User\Facade\UserFacade;
use Illuminate\Support\Facades\Session;

class PasswordResetLinkController extends Controller
{
    public function create()
    {
        return view('user::auth.forgot-password');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = UserFacade::findUserByEmail($request->email);
        if (!$user) {
            return back()->withErrors(['email' => 'This email is not registered.']);
        }

        // Generate and store the verification code for custom flow
        $code = UserFacade::sendResetCode($user);
        Session::put('reset_code', $code);
        Session::put('reset_email', $request->email);

        // Also flash the code for views that alert on session('code')
        Session::flash('code', (string) $code);

        return redirect()->route('password.verify.form')
            ->with('status', 'Your verification code is: ' . $code);
    }

    public function showVerifyForm()
    {
        return view('user::auth.verify-code');
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $email = Session::get('reset_email');
        $code = Session::get('reset_code');

        if (!$email || !$code) {
            return redirect()->route('password.request')
                ->withErrors(['code' => 'The verification session has expired. Please request a new code.']);
        }

        if (UserFacade::verifyResetCode($email, (int) $request->code)) {
            Session::forget(['reset_code', 'reset_email']);
            return redirect()->route('password.reset', ['email' => $email])
                ->with('status', 'Code verified successfully. You can reset your password.');
        }

        return back()->withErrors(['code' => 'The verification code is incorrect.']);
    }
}
