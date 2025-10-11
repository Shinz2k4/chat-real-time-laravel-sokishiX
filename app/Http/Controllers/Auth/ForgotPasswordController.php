<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PasswordReset;
use App\Mail\PasswordResetMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    /**
     * Display the form to request a password reset link.
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Send a reset link to the given user.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không hợp lệ.',
            'email.exists' => 'Không tìm thấy tài khoản với địa chỉ email này.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $email = $request->email;

        // Check if user exists
        $user = User::where('email', $email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'Không tìm thấy tài khoản với địa chỉ email này.'])->withInput();
        }

        // Delete old reset tokens for this email
        PasswordReset::where('email', $email)->delete();

        // Generate new reset token
        $token = PasswordReset::generateToken();
        
        // Store reset data
        PasswordReset::create([
            'email' => $email,
            'token' => $token,
            'created_at' => now()->toDateTimeString(),
        ]);

        // Generate reset URL
        $resetUrl = route('password.reset', ['token' => $token, 'email' => $email]);
        $expiresAt = now()->addHour()->format('d/m/Y H:i');

        // Send reset email
        Mail::to($email)->send(new PasswordResetMail($resetUrl, $email, $expiresAt));

        return back()->with('status', 'Chúng tôi đã gửi link đặt lại mật khẩu đến email của bạn!');
    }
}
