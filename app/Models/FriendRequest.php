<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class FriendRequest extends Model
{
    protected $connection = 'mongodb';
    protected $primaryKey = '_id';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'from_user_id',
        'to_user_id',
        'status', // pending | accepted | declined
        'created_at',
        'updated_at',
    ];

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id', '_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id', '_id');
    }
}


