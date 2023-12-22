<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BasePatch extends Model
{
    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'basepatches';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'build',
    ];
}
