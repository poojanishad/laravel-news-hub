<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class User extends Model
{

    protected $fillable = ['id'];

    protected $keyType = 'string';
    public $incrementing = false;
    
    protected static function booted()
    {
        static::creating(function ($user) {
            if (!$user->id) {
                $user->id = (string) Str::uuid();
            }
        });
    }

    public function preference()
    {
        return $this->hasOne(UserPreference::class);
    }
}
