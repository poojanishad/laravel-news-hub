<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class User extends Model
{

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id'];

    public function preference()
    {
        return $this->hasOne(UserPreference::class);
    }
}
