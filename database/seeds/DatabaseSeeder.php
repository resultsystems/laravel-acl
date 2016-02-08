<?php

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use ResultSystems\Acl\Branch;
use ResultSystems\Acl\Permission;
use ResultSystems\Acl\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        factory(User::class, 5)->create();
        factory(Permission::class, 30)->create();
        factory(Role::class, 5)->create();
        factory(Branch::class, 3)->create();
        $users = User::all();
        foreach ($users as $user) {
            $branches = Branch::all()->random(2);
            $roles    = Role::all()->random(rand(2, 3));
            foreach ($branches as $branch) {
                foreach ($roles as $role) {
                    $user->branches()->attach($branch->id, ["role_id" => $role->id]);
                }
            }
            $role = Role::all()->random(1);
            $user->roles()->attach($role->id);
            $permissions = Permission::all()->random(5);
            foreach ($permissions as $permission) {
                $user->permissions()->attach($permission->id);
            }
        }
        $roles = Role::all();
        foreach ($roles as $role) {
            $permissions = Permission::all()->random(rand(3, 8));
            foreach ($permissions as $permission) {
                $role->permissions()->attach($permission->id);
            }
        }
        // $this->call(UserTableSeeder::class);

        Model::reguard();
    }
}
