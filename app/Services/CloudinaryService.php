<?php

namespace App\Services;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    /**
     * Upload avatar image to Cloudinary
     *
     * @param UploadedFile $file
     * @param string $userId
     * @return array
     */
    public static function uploadAvatar(UploadedFile $file, string $userId): array
    {
        try {
            // Validate file
            if (!$file->isValid()) {
                throw new \Exception('File upload không hợp lệ.');
            }

            // Check file type
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($file->getMimeType(), $allowedTypes)) {
                throw new \Exception('Chỉ chấp nhận file ảnh (JPEG, PNG, GIF, WebP).');
            }

            // Check file size (max 10MB)
            if ($file->getSize() > 10 * 1024 * 1024) {
                throw new \Exception('Kích thước file không được vượt quá 10MB.');
            }

            // Generate unique filename
            $filename = 'avatar_' . $userId . '_' . time() . '.' . $file->getClientOriginalExtension();

            // Upload to Cloudinary with transformations
            $result = Cloudinary::upload($file->getRealPath(), [
                'public_id' => 'laravel-chat-app/avatars/' . $filename,
                'folder' => 'laravel-chat-app/avatars',
                'transformation' => [
                    'width' => 400,
                    'height' => 400,
                    'crop' => 'fill',
                    'gravity' => 'face',
                    'quality' => 'auto',
                    'format' => 'auto'
                ]
            ]);

            return [
                'success' => true,
                'public_id' => $result->getPublicId(),
                'secure_url' => $result->getSecurePath(),
                'url' => $result->getPath(),
                'filename' => $filename
            ];

        } catch (\Exception $e) {
            Log::error('Cloudinary upload error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Delete image from Cloudinary
     *
     * @param string $publicId
     * @return array
     */
    public static function deleteImage(string $publicId): array
    {
        try {
            $result = Cloudinary::destroy($publicId);
            
            return [
                'success' => $result['result'] === 'ok',
                'message' => $result['result'] === 'ok' ? 'Xóa ảnh thành công' : 'Không thể xóa ảnh'
            ];

        } catch (\Exception $e) {
            Log::error('Cloudinary delete error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get optimized avatar URL
     *
     * @param string $publicId
     * @param array $transformations
     * @return string
     */
    public static function getAvatarUrl(string $publicId, array $transformations = []): string
    {
        $defaultTransformations = [
            'width' => 150,
            'height' => 150,
            'crop' => 'fill',
            'gravity' => 'face',
            'quality' => 'auto',
            'format' => 'auto'
        ];

        $transformations = array_merge($defaultTransformations, $transformations);

        // Build transformation string
        $transformationString = "w_{$transformations['width']},h_{$transformations['height']},c_{$transformations['crop']},g_{$transformations['gravity']},q_{$transformations['quality']},f_{$transformations['format']}";
        
        // Get cloud name from config
        $cloudName = config('cloudinary.cloud_name') ?: 'do8gfnops';
        
        // Build URL manually
        return "https://res.cloudinary.com/{$cloudName}/image/upload/{$transformationString}/{$publicId}";
    }

    /**
     * Get default avatar URL
     *
     * @return string
     */
    public static function getDefaultAvatarUrl(): string
    {
        // You can use a default avatar from Cloudinary or a local default image
        return 'https://via.placeholder.com/150/6366f1/ffffff?text=U';
    }

    /**
     * Extract public ID from Cloudinary URL
     *
     * @param string $url
     * @return string|null
     */
    public static function extractPublicId(string $url): ?string
    {
        // Extract public ID from Cloudinary URL
        // Format: https://res.cloudinary.com/cloud_name/image/upload/v1234567890/folder/filename.jpg
        $pattern = '/\/upload\/(?:v\d+\/)?(.+?)(?:\.[^.]+)?$/';
        
        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }
        
        return null;
    }

    /**
     * Check if URL is a Cloudinary URL
     *
     * @param string $url
     * @return bool
     */
    public static function isCloudinaryUrl(string $url): bool
    {
        return strpos($url, 'res.cloudinary.com') !== false;
    }
}
