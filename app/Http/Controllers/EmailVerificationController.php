<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmailVerification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailVerificationMail;

class EmailVerificationController extends Controller
{
    /**
     * Show the email verification form
     */
    public function showVerificationForm(Request $request)
    {
        $email = $request->query('email');
        
        if (!$email) {
            return redirect()->route('register')->with('error', 'Email không hợp lệ.');
        }

        return view('auth.verify-email', compact('email'));
    }

    /**
     * Verify the email with the provided code
     */
    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6',
        ]);

        $email = $request->email;
        $code = $request->code;

        // Find the verification record
        $verification = EmailVerification::where('email', $email)
            ->where('code', $code)
            ->where('verified', false)
            ->first();

        if (!$verification) {
            return back()->withErrors(['code' => 'Mã xác thực không đúng hoặc đã được sử dụng.'])->withInput();
        }

        // Check if code is expired
        if ($verification->isExpired()) {
            return back()->withErrors(['code' => 'Mã xác thực đã hết hạn. Vui lòng yêu cầu mã mới.'])->withInput();
        }

        // Mark as verified
        $verification->update([
            'verified' => true,
            'updated_at' => now()->toDateTimeString(),
        ]);

        // Get pending user data from session
        $pendingUser = session('pending_user');
        
        if (!$pendingUser) {
            return redirect()->route('register')->with('error', 'Phiên đăng ký đã hết hạn. Vui lòng đăng ký lại.');
        }

        try {
            // Create the user using new instance
            $user = new User();
            $user->name = $pendingUser['name'];
            $user->email = $pendingUser['email'];
            $user->phone = $pendingUser['phone'];
            $user->password = $pendingUser['password'];
            $user->profile_image = $pendingUser['profile_image'];
            $user->created_at = now()->toDateTimeString();
            $user->updated_at = now()->toDateTimeString();
            $user->save();

            // Check if user was created successfully
            if (!$user || !($user instanceof User)) {
                throw new \Exception('Không thể tạo tài khoản. Vui lòng thử lại.');
            }

            // Clear pending user data from session
            session()->forget('pending_user');

            // Login the user
            auth()->login($user);

            return redirect()->route('home')->with('success', 'Đăng ký thành công! Chào mừng bạn đến với SokishiX Chat!');
            
        } catch (\Exception $e) {
            return back()->withErrors(['code' => 'Có lỗi xảy ra khi tạo tài khoản: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Resend verification code
     */
    public function resendVerification(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->email;

        // Check if there's a pending user
        $pendingUser = session('pending_user');
        if (!$pendingUser || $pendingUser['email'] !== $email) {
            return back()->with('error', 'Không tìm thấy thông tin đăng ký. Vui lòng đăng ký lại.');
        }

        // Delete old verification codes for this email
        EmailVerification::where('email', $email)->delete();

        // Generate new verification code
        $verificationCode = EmailVerification::generateCode();
        
        // Store new verification data
        EmailVerification::create([
            'email' => $email,
            'code' => $verificationCode,
            'expires_at' => now()->addMinutes(10),
            'verified' => false,
            'created_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
        ]);

        // Send new verification email
        Mail::to($email)->send(new EmailVerificationMail($verificationCode, $email));

        return back()->with('success', 'Mã xác thực mới đã được gửi đến email của bạn.');
    }
}