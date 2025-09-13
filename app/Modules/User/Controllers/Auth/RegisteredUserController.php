<?php

namespace App\Modules\User\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Modules\User\Facade\UserFacade;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('user::auth.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = UserFacade::register($request->all());

        Auth::login($user);

        return redirect()->route('dashboard')->with('status', 'Registration successful!');
    }

    
}
