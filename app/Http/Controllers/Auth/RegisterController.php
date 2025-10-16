<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Models\EmailVerification;
use App\Mail\EmailVerificationMail;
use App\Services\CloudinaryService;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $user = $this->create($request->all());

        // Don't login here, redirect to verification page instead
        return $user;
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:20', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            // allow up to 10MB, accept common image types
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:10240'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        // Handle profile image upload
        $profileImageUrl = 'default_image.png';
        
        // Always retrieve file from request to ensure UploadedFile instance
        $uploadedFile = request()->file('profile_image');
        if ($uploadedFile) {
            // Generate a temporary user ID for upload
            $tempUserId = 'temp_' . time() . '_' . rand(1000, 9999);
            
            // Upload to Cloudinary
            $uploadResult = CloudinaryService::uploadAvatar($uploadedFile, $tempUserId);
            
            if ($uploadResult['success']) {
                $profileImageUrl = $uploadResult['secure_url'];
            } else {
                // If upload fails, use default image
                $profileImageUrl = 'default_image.png';
                // You might want to log this error or show a message to user
                \Log::warning('Failed to upload profile image: ' . $uploadResult['error']);
            }
        }

        // Generate verification code
        $verificationCode = EmailVerification::generateCode();
        
        // Store verification data
        EmailVerification::create([
            'email' => $data['email'],
            'code' => $verificationCode,
            'expires_at' => now()->addMinutes(10),
            'verified' => false,
            'created_at' => now()->toDateTimeString(),
            'updated_at' => now()->toDateTimeString(),
        ]);

        // Send verification email
        Mail::to($data['email'])->send(new EmailVerificationMail($verificationCode, $data['email']));

        // Store user data in session for later verification
        session([
            'pending_user' => [
                'name' => $data['name'],
                'username' => $data['username'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password' => Hash::make($data['password']),
                'profile_image' => $profileImageUrl,
            ]
        ]);

        // Redirect to verification page
        return redirect()->route('verify.email.form', ['email' => $data['email']])
                        ->with('success', 'Mã xác thực đã được gửi đến email của bạn. Vui lòng kiểm tra hộp thư.');
    }
}
