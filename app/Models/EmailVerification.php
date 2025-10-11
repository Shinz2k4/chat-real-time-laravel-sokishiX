<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseMongoModel as Model;

class EmailVerification extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $primaryKey = '_id';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'email',
        'code',
        'expires_at',
        'verified',
    ];

    protected $dates = ['expires_at'];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return '_id';
    }

    /**
     * Check if the verification code is expired
     */
    public function isExpired()
    {
        return $this->expires_at < now();
    }

    /**
     * Generate a random 6-digit code
     */
    public static function generateCode()
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
}
