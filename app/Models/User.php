<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Services\CloudinaryService;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $connection = 'mongodb';
    protected $primaryKey = '_id';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'password',
        'profile_image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return '_id';
    }

    /**
     * Get the collection name for the model
     */
    public function getTable()
    {
        return 'users';
    }

    /**
     * Get the avatar URL
     *
     * @param array $transformations
     * @return string
     */
    public function getAvatarUrl(array $transformations = []): string
    {
        if ($this->profile_image && CloudinaryService::isCloudinaryUrl($this->profile_image)) {
            // Transform the full secure URL directly to avoid wrong cloud name issues
            return CloudinaryService::transformFullUrl($this->profile_image, $transformations);
        }

        // Return default avatar if no profile image or not a Cloudinary URL
        return CloudinaryService::getDefaultAvatarUrl();
    }

    /**
     * Get the small avatar URL (for lists)
     *
     * @return string
     */
    public function getSmallAvatarUrl(): string
    {
        return $this->getAvatarUrl([
            'width' => 50,
            'height' => 50
        ]);
    }

    /**
     * Get the medium avatar URL (for chat headers)
     *
     * @return string
     */
    public function getMediumAvatarUrl(): string
    {
        return $this->getAvatarUrl([
            'width' => 100,
            'height' => 100
        ]);
    }

    /**
     * Get the large avatar URL (for profile pages)
     *
     * @return string
     */
    public function getLargeAvatarUrl(): string
    {
        return $this->getAvatarUrl([
            'width' => 300,
            'height' => 300
        ]);
    }

    /**
     * Check if user has a custom avatar
     *
     * @return bool
     */
    public function hasCustomAvatar(): bool
    {
        return $this->profile_image && 
               $this->profile_image !== 'default_image.png' && 
               CloudinaryService::isCloudinaryUrl($this->profile_image);
    }

}
