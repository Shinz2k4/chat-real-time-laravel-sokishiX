<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseMongoModel as Model;

class PasswordReset extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $primaryKey = '_id';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'email',
        'token',
        'created_at',
    ];

    protected $dates = ['created_at'];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return '_id';
    }

    /**
     * Check if the reset token is expired (valid for 1 hour)
     */
    public function isExpired()
    {
        return $this->created_at < now()->subHour();
    }

    /**
     * Generate a random reset token
     */
    public static function generateToken()
    {
        return bin2hex(random_bytes(32));
    }
}
