<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\BaseMongoModel as Model;


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

    // Let Jenssegers handle date conversion; declare date attributes
    protected $dates = ['created_at', 'updated_at'];

    // Force Eloquent to manage timestamps
    public $timestamps = true;

    protected function serializeDate(\DateTimeInterface $date): string
    {
        return $date->format(\DateTimeInterface::ATOM);
    }

    public function fromContact()
    {
        return $this->belongsTo(User::class, 'from', '_id');
    }

    public function toContact()
    {
        return $this->belongsTo(User::class, 'to', '_id');
    }
    public function getRouteKeyName(): string
    {
        return '_id';
    }

}
