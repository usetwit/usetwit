<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::create(['name' => 'users.view.self']);
        Permission::create(['name' => 'users.update.self.address']);
        Permission::create(['name' => 'users.update.self.personal-profile']);
        Permission::create(['name' => 'users.update.self.company-profile']);
        Permission::create(['name' => 'users.update.self.profile-image']);
        Permission::create(['name' => 'users.update.address']);
        Permission::create(['name' => 'users.update.personal-profile']);
        Permission::create(['name' => 'users.update.company-profile']);
        Permission::create(['name' => 'users.update.profile-image']);
        Permission::create(['name' => 'company.update']);

        $methods = ['create', 'update', 'view', 'delete', 'restore'];
        $modules = ['users', 'roles', 'locations', 'calendars', 'sales-orders', 'invoices'];

        foreach ($modules as $module) {
            foreach ($methods as $method) {
                Permission::create(['name' => $module.'.'.$method]);
            }
        }

        Role::create(['name' => 'admin'])->givePermissionTo(Permission::all());
        Role::create(['name' => 'customer']);
        Role::create(['name' => 'design']);
        Role::create(['name' => 'sales']);
        Role::create(['name' => 'purchasing']);
        Role::create(['name' => 'finance']);
        Role::create(['name' => 'operator']);
        Role::create(['name' => 'supervisor']);
        Role::create(['name' => 'manager']);
    }
}
