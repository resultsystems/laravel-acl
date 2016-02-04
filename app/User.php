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

    /**
     * Pegas as filiais
     *
     * @return Collection
     */
    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'groups')
            ->withPivot(['role_id'])
            ->with('roles.permissions');
    }

    /**
     * Pega as Roles
     *
     * @return Collection
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, "role_user");
    }

    /**
     * Pega as permissões
     *
     * @return Collection
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, "permission_user");
    }

    /**
     * Verifica se o usuário tem a(s) permissão(ões) :permission
     *
     * Caso seja passado uma branch (filial)
     * Será verificado as permissões apenas nesta branch
     *
     * @param  string  $permission
     * @param  bool    $any
     * @param  int     $branch_id
     *
     * @return boolean
     */
    public function hasPermission($checkPermissions, $any = true, $branch_id = null)
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
            return $this->checkPermissionsByBranches($user->branches, $branch_id, $checkPermissions, $any);
        }

        if ($this->checkPermissions($user->permissions, $checkPermissions, $any)) {
            return true;
        }

        if ($this->checkPermissionsInRoles($user->roles, $checkPermissions, $any)) {
            return true;
        }

        return false;
    }

    /**
     * Verifica se o usuário tem a(s) permissão(ões) :checkPermissions na branch :branch_id
     *
     * @param  array         $branches
     * @param  int           $branch_id
     * @param  array|string  $checkPermissions
     * @param  bool          $any
     *
     * @return bool
     */
    private function checkPermissionsByBranches($branches, $branch_id, $checkPermissions, $any)
    {
        foreach ($branches as $branch) {
            if ($branch->id != $branch_id) {
                continue;
            }

            if ($this->checkPermissionsInRoles($branch->roles, $checkPermissions, $any)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verifica se o usuário tem a(s) permissão(ões) :checkPermissions
     * Dentro de de alguma das roles :roles
     *
     * @param  array        $roles
     * @param  array|string $checkPermissions
     * @param  bool          $any
     *
     * @return bool
     */
    private function checkPermissionsInRoles($roles, $checkPermissions, $any)
    {
        foreach ($roles as $role) {
            if ($this->checkPermissions($role->permissions, $checkPermissions, $any)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checka se a(s) permissão(ões) :checkPermissions
     * Está em :permissions
     *
     * @param  array        $permissions
     * @param  array|string $checkPermissions
     * @param  bool         $any
     *
     * @return bool
     */
    private function checkPermissions($permissions, $checkPermissions, $any)
    {
        if (!is_array($checkPermissions)) {
            $checkPermissions = array($checkPermissions);
        }

        $hasPermission = false;
        foreach ($checkPermissions as $permission) {
            $hasPermission = false;

            foreach ($permissions as $p) {
                if ($p->slug == $permission) {
                    if ($any) {
                        return true;
                    }
                    $hasPermission = true;
                    continue 2;
                }
            }
        }

        return $hasPermission;
    }
}
