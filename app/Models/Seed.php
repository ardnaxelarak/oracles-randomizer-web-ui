<?php

namespace App\Models;

use Hashids\Hashids;
use Illuminate\Database\Eloquent\Model;

class Seed extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are not mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    public static function boot() {
        parent::boot();

        $hasher = new Hashids(env('HOSTNAME', 'localhost'), 10);

        static::created(function ($seed) use ($hasher) {
            $seed->hash = $hasher->encode($seed->id);
            $seed->save();
        });
    }
}
