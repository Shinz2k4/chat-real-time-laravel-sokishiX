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
     * Upload generic message attachment (image/file) to Cloudinary
     * - Images go to images folder with image upload API
     * - Non-images go to raw folder with resource_type raw
     */
    public static function uploadMessageAttachment(UploadedFile $file, string $userId): array
    {
        try {
            if (!$file->isValid()) {
                throw new \Exception('File upload không hợp lệ.');
            }

            // Limit 20MB
            if ($file->getSize() > 20 * 1024 * 1024) {
                throw new \Exception('Kích thước file không được vượt quá 20MB.');
            }

            $mime = $file->getMimeType();
            $ext = $file->getClientOriginalExtension();
            $base = 'laravel-chat-app/messages/'.date('Y/m/d')."/{$userId}_".time();

            if (str_starts_with($mime, 'image/')) {
                $result = Cloudinary::upload($file->getRealPath(), [
                    'public_id' => $base,
                    'folder' => 'laravel-chat-app/messages',
                    'transformation' => [
                        'quality' => 'auto',
                        'format' => 'auto'
                    ]
                ]);
            } else {
                // For non-image, use raw resource type
                $result = Cloudinary::uploadFile($file->getRealPath(), [
                    'public_id' => $base,
                    'folder' => 'laravel-chat-app/messages',
                    'resource_type' => 'raw'
                ]);
            }

            return [
                'success' => true,
                'secure_url' => $result->getSecurePath(),
                'url' => $result->getPath(),
                'public_id' => $result->getPublicId(),
                'mime' => $mime,
                'ext' => $ext,
                'bytes' => $file->getSize(),
                'original_name' => $file->getClientOriginalName()
            ];
        } catch (\Exception $e) {
            Log::error('Cloudinary upload message attachment error: ' . $e->getMessage());
            return [ 'success' => false, 'error' => $e->getMessage() ];
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
        $cloudName = config('cloudinary.cloud_name');
        if (!$cloudName) {
            // Fallback: build relative path so caller can prepend or use transformFullUrl
            return "/image/upload/{$transformationString}/{$publicId}";
        }
        // Build URL manually when cloud name is known
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
     * Transform an existing Cloudinary URL by injecting transformation string.
     */
    public static function transformFullUrl(string $url, array $transformations = []): string
    {
        $defaultTransformations = [
            'width' => 150,
            'height' => 150,
            'crop' => 'fill',
            'gravity' => 'face',
            'quality' => 'auto',
            'format' => 'auto'
        ];
        $t = array_merge($defaultTransformations, $transformations);
        $trans = "w_{$t['width']},h_{$t['height']},c_{$t['crop']},g_{$t['gravity']},q_{$t['quality']},f_{$t['format']}";

        // Insert transforms BEFORE version segment (correct Cloudinary order)
        // From: /upload/v1234/path
        // To:   /upload/w_..../v1234/path
        return preg_replace('/\/upload\/(v\d+\/)?/','/upload/'.$trans.'/$1', $url, 1) ?: $url;
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
