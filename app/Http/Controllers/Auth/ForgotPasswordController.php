<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.forgot_password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|exists:users,phone',
        ], [
            'phone.exists' => 'No user account found registered with this phone number.',
        ]);

        $user = User::where('phone', $request->phone)->first();

        // Generate temporary 6-digit reset code
        $resetCode = rand(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($resetCode),
                'created_at' => now(),
            ]
        );

        return redirect()->route('password.reset', ['phone' => $user->phone])
            ->with('success', "Security reset code generated for {$user->phone}. Demo verification code: {$resetCode}");
    }

    public function showResetForm(Request $request)
    {
        $phone = $request->query('phone');
        return view('auth.reset_password', compact('phone'));
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'phone'    => 'required|string|exists:users,phone',
            'code'     => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::where('phone', $request->phone)->first();
        $record = DB::table('password_reset_tokens')->where('email', $user->email)->first();

        if (!$record || !Hash::check($request->code, $record->token)) {
            return redirect()->back()
                ->withErrors(['code' => 'Invalid security reset code entered.'])
                ->withInput();
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        DB::table('password_reset_tokens')->where('email', $user->email)->delete();

        return redirect()->route('login')
            ->with('success', 'Your password has been updated! Please log in with your phone number and new password.');
    }
}
