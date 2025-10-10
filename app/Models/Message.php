<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Message extends Model
{
    protected $guarded = [];
    protected $connection = 'mongodb';
    protected $primaryKey = '_id';
    protected $keyType = 'string';
    public $incrementing = false;
    use HasFactory;

    protected $fillable = [
        'from',
        'to', 
        'text',
        'read'
    ];

    public function fromContact()
    {
        return $this->belongsTo(User::class, 'from', '_id');
    }

    public function toContact()
    {
        return $this->belongsTo(User::class, 'to', '_id');
    }
}
