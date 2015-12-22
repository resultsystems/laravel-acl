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

    /**
     * Verifica se o usuário tem a permissão :permission
     *
     * Caso seja passado uma branch (filial)
     * Será verificado as permissões apenas nesta branch
     *
     * @param  string  $permission
     * @param  int     $branch_id
     *
     * @return boolean
     */
    public function hasPermission($permission, $branch_id = null)
    {
        $user_id = $this->id;
        $user    = $this->with(['branches' => function ($query) use ($branch_id, $user_id) {
            $query
                ->where("id", "=", $branch_id)
                ->with(['roles' => function ($q) use ($user_id) {
                    $q->where("user_id", "=", $user_id);
                }]);
        }, "roles.permissions", "permissions"])->where("id", $this->id)->first();

        if ($branch_id) {
            return $this->checkPermissionByBranches($user->branches, $branch_id, $permission);
        }

        if ($this->checkPermission($user->permissions, $permission)) {
            return true;
        }

        if ($this->checkPermissionInRoles($user->roles, $permission)) {
            return true;
        }

        return false;
    }

    /**
     * Verifica se o usuário tem a permissão :permission na branch :branch_id
     *
     * @param  array  $branches
     * @param  int    $branch_id
     * @param  string $permission
     *
     * @return bool
     */
    private function checkPermissionByBranches($branches, $branch_id, $permission)
    {
        foreach ($branches as $branch) {
            if ($branch->id != $branch_id) {
                echo "Filial: " . $branch->id . "<br><br>";
                continue;
            }
            if ($this->checkPermissionInRoles($branch->roles, $permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verifica se o usuário tem a permissão :permission
     * Dentro de de alguma das roles :roles
     *
     * @param  array  $roles
     * @param  string $permission
     *
     * @return bool
     */
    private function checkPermissionInRoles($roles, $permission)
    {
        foreach ($roles as $role) {
            if ($this->checkPermission($role->permissions, $permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checka se a permissão :permission
     * Está em :permissions
     *
     * @param  array  $permissions
     * @param  string $permission
     *
     * @return bool
     */
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
