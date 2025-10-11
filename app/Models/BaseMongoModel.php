<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Jenssegers\Mongodb\Eloquent\Model as MongoModel;
use MongoDB\BSON\UTCDateTime;

class BaseMongoModel extends MongoModel
{
    /**
     * Return a fresh timestamp instance suitable for MongoDB driver (UTCDateTime in ms).
     */
    public function freshTimestamp(): UTCDateTime
    {
        // Get current timestamp in milliseconds
        $timestamp = (int) (microtime(true) * 1000);
        return new UTCDateTime($timestamp);
    }

    /**
     * Convert a DateTime to a value MongoDB understands (UTCDateTime).
    */
    public function fromDateTime($value): UTCDateTime
    {
        if ($value instanceof UTCDateTime) {
            return $value;
        }

        if ($value instanceof \DateTimeInterface) {
            $carbon = Carbon::instance($value);
        } else {
            $carbon = Carbon::parse($value);
        }
        return new UTCDateTime((int) ($carbon->timestamp * 1000));
    }
}


