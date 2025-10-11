<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Models\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Display the password reset view for the given token.
     */
    public function showResetForm(Request $request, $token = null)
    {
        $email = $request->query('email');
        
        if (!$token || !$email) {
            return redirect()->route('password.request')->with('error', 'Link đặt lại mật khẩu không hợp lệ.');
        }

        // Check if token exists and is not expired
        $passwordReset = PasswordReset::where('email', $email)
            ->where('token', $token)
            ->first();

        if (!$passwordReset || $passwordReset->isExpired()) {
            return redirect()->route('password.request')->with('error', 'Link đặt lại mật khẩu đã hết hạn hoặc không hợp lệ.');
        }

        return view('auth.passwords.reset', compact('token', 'email'));
    }

    /**
     * Reset the given user's password.
     */
    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ], [
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $email = $request->email;
        $token = $request->token;
        $password = $request->password;

        // Check if token exists and is not expired
        $passwordReset = PasswordReset::where('email', $email)
            ->where('token', $token)
            ->first();

        if (!$passwordReset || $passwordReset->isExpired()) {
            return back()->withErrors(['email' => 'Link đặt lại mật khẩu đã hết hạn hoặc không hợp lệ.'])->withInput();
        }

        // Find user
        $user = User::where('email', $email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'Không tìm thấy tài khoản.'])->withInput();
        }

        // Update password
        $user->update([
            'password' => Hash::make($password),
            'updated_at' => now()->toDateTimeString(),
        ]);

        // Delete reset token
        $passwordReset->delete();

        // Login user
        auth()->login($user);

        return redirect()->route('home')->with('success', 'Mật khẩu đã được đặt lại thành công!');
    }
}
