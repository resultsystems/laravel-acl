<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'groups');
        //->wherePivot('type', 'reminder_customer');
        //->wherePivot('branch', 'reminder_customer');
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
