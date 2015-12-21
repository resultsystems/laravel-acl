<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'groups');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'groups');
    }

    public function groups()
    {
        return $this->hasManyThrough(Role::class, User::class);
    }
}
