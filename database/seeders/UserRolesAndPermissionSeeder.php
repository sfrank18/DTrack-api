<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserRolesAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
         // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        // Permission::create(['name' => 'edit articles']);
        // Permission::create(['name' => 'delete articles']);
        // Permission::create(['name' => 'publish articles']);
        // Permission::create(['name' => 'unpublish articles']);

        // create roles and assign created permissions

        // this can be done as separate statements
        // $role = Role::create(['name' => 'Super Admin']);
        // $role->givePermissionTo('edit articles');

        // // or may be done by chaining
        // $role = Role::create(['name' => 'employee']);
        // $role = Role::create(['name' => 'admin']);
        //     // ->givePermissionTo(['publish articles', 'unpublish articles']);

        // $role = Role::create(['name' => 'super-admin']);
        // $role->givePermissionTo(Permission::all());

        $roles = [
                [
                'name'=>"Super-admin"
                ],
                [
                'name'=>"Admin"
                ],
                [
                'name'=>"Employee"
                ]
            ];

        Role::upsert($roles,['name'],['name']);

    }
}
