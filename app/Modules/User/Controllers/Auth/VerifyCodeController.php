<?php

namespace App\Modules\User\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Modules\User\Facade\UserFacade;

class VerifyCodeController extends Controller
{
    public function show(Request $request)
    {
        // Generate a 6-digit code
        $code = rand(100000, 999999);

        // Generate nonce and store code in cache
        $nonce = Str::random(32);
        cache()->put("verify_email_code:{$nonce}", $code, now()->addMinutes(10));

        // Pass nonce and code via session for alert
        return view('user::auth.verify-email', [
            'alert_code' => $code,
            'nonce' => $nonce,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
            'nonce' => 'required|string',
        ]);

        $cachedCode = cache()->get("verify_email_code:{$request->nonce}");

        if (!$cachedCode) {
            return back()->withErrors(['code' => 'Verification code expired or invalid.']);
        }

        if ($cachedCode == $request->code) {
            // Code verified, delete nonce
            cache()->forget("verify_email_code:{$request->nonce}");

            return redirect()->route('password.reset.form')
                ->with('status', 'Verification successful. Please reset your password.');
        }

        return back()->withErrors(['code' => 'Invalid verification code.']);
    }
}
