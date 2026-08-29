<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectUserByRole(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'phone'    => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt(['phone' => $credentials['phone'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            return $this->redirectUserByRole($user)
                ->with('success', "Welcome back, {$user->name}!");
        }

        return redirect()->back()
            ->withErrors(['phone' => 'Invalid phone number or password credentials.'])
            ->withInput($request->only('phone'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }

    protected function redirectUserByRole($user)
    {
        if ($user->isCustomer()) {
            return \Illuminate\Support\Facades\Route::has('shop.index') 
                ? redirect()->route('shop.index') 
                : redirect()->route('login')->with('success', 'Customer login successful!');
        }

        return redirect()->route('dashboard');
    }
}
