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

        Permission::create(['name' => 'users.create']);
        Permission::create(['name' => 'users.update']);
        Permission::create(['name' => 'users.view']);
        Permission::create(['name' => 'users.view.self']);
        Permission::create(['name' => 'users.update.self.address']);
        Permission::create(['name' => 'users.update.self.personal-profile']);
        Permission::create(['name' => 'users.update.self.company-profile']);
        Permission::create(['name' => 'users.update.self.profile-image']);
        Permission::create(['name' => 'users.delete']);
        Permission::create(['name' => 'users.restore']);
        Permission::create(['name' => 'sales-orders.create']);
        Permission::create(['name' => 'sales-orders.update']);
        Permission::create(['name' => 'sales-orders.delete']);
        Permission::create(['name' => 'calendars.update']);

        Role::create(['name' => 'Admin'])->givePermissionTo(Permission::all());
        Role::create(['name' => 'Design']);
        Role::create(['name' => 'Sales']);
        Role::create(['name' => 'Purchasing']);
        Role::create(['name' => 'Finance']);
        Role::create(['name' => 'Operator']);
        Role::create(['name' => 'Supervisor']);
        Role::create(['name' => 'Manager']);
    }
}
