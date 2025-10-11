<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function index()
    {
        $user = Auth::user();
        return view('profile.index', compact('user'));
    }

    /**
     * Show the form for editing the profile.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'name.required' => 'Vui lòng nhập tên.',
            'name.max' => 'Tên không được vượt quá 255 ký tự.',
            'phone.max' => 'Số điện thoại không được vượt quá 20 ký tự.',
            'profile_image.image' => 'File phải là hình ảnh.',
            'profile_image.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif.',
            'profile_image.max' => 'Kích thước hình ảnh không được vượt quá 2MB.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['name', 'phone']);

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            // Delete old avatar from Cloudinary if exists
            if ($user->hasCustomAvatar()) {
                // Note: In a real app, you might want to delete the old image from Cloudinary
                // For now, we'll just upload the new one
            }

            // Upload new avatar to Cloudinary
            $uploadResult = CloudinaryService::uploadAvatar($request->file('profile_image'), $user->_id);
            
            if ($uploadResult['success']) {
                $data['profile_image'] = $uploadResult['secure_url'];
            } else {
                return back()->withErrors(['profile_image' => 'Không thể tải lên ảnh đại diện. Vui lòng thử lại.'])->withInput();
            }
        }

        $user->update(array_merge($data, [
            'updated_at' => now()->toDateTimeString(),
        ]));

        return redirect()->route('profile.index')->with('success', 'Thông tin cá nhân đã được cập nhật thành công!');
    }

    /**
     * Show the form for changing password.
     */
    public function changePassword()
    {
        return view('profile.change-password');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.'])->withInput();
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password),
            'updated_at' => now()->toDateTimeString(),
        ]);

        return redirect()->route('profile.index')->with('success', 'Mật khẩu đã được thay đổi thành công!');
    }

    /**
     * Delete the user's profile image.
     */
    public function deleteAvatar()
    {
        $user = Auth::user();

        if ($user->hasCustomAvatar()) {
            // Note: In a real app, you might want to delete the image from Cloudinary
            // For now, we'll just set it to default
            $user->update([
                'profile_image' => 'default_image.png',
                'updated_at' => now()->toDateTimeString(),
            ]);

            return redirect()->route('profile.index')->with('success', 'Ảnh đại diện đã được xóa!');
        }

        return redirect()->route('profile.index')->with('info', 'Bạn chưa có ảnh đại diện tùy chỉnh.');
    }
}
