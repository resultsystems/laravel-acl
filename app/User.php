<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;

class User extends Model implements AuthenticatableContract,
AuthorizableContract,
CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'groups')
            ->withPivot(['role_id'])
            ->with('roles.permissions');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, "role_user");
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, "permission_user");
    }

    public function permissionsByBranchId($id)
    {
        return $this->branches(function ($query) use ($id) {
            $query->where("id", $id);
        });
    }

    public function hasPermission($permission, $branch_id = null)
    {
        $user = $this->with(['branches.roles' => function ($query) {
            $query->where("id", "=", 1);
        }, "roles.permissions", "permissions"])->where("id", $this->id)->first();
        if ($this->checkPermission($user->permissions, $permission)) {
            return true;
        }

        if ($this->checkPermissionInRoles($user->roles, $permission)) {
            return true;
        }

        if (is_null($branch_id)) {
            return false;
        }

        foreach ($user->branches as $branch) {
            if ($this->checkPermissionInRoles($branch->roles)) {
                return true;
            }
        }

        return false;
    }

    private function checkPermissionInRoles($roles, $permission)
    {
        foreach ($roles as $role) {
            if ($this->checkPermission($role->permissions, $permission)) {
                return true;
            }
        }

        return false;
    }

    private function checkPermission($permissions, $permission)
    {
        foreach ($permissions as $p) {
            if ($p->slug == $permission) {
                return true;
            }
        }

        return false;
    }
}
