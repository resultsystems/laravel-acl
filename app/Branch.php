<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'branch_group');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'branch_group');
    }
}
