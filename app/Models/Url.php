<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Builder;

class Url extends Model
{
    use Prunable;

    public $timestamps = false;

    protected $fillable = ['unencoded', 'encoded', 'expires_at'];

    public function prunable(): Builder
    {
        return static::where('expires_at', '<=', now());
    }
}