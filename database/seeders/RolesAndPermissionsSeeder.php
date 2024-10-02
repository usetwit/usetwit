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
        Permission::create(['name' => 'users.edit']);
        Permission::create(['name' => 'users.delete']);
        Permission::create(['name' => 'sales-orders.create']);
        Permission::create(['name' => 'sales-orders.edit']);
        Permission::create(['name' => 'sales-orders.delete']);
        Permission::create(['name' => 'calendars.edit']);

        Role::create(['name' => 'system'])->givePermissionTo(Permission::all());
        Role::create(['name' => 'admin'])->givePermissionTo(Permission::all());
        Role::create(['name' => 'design']);
        Role::create(['name' => 'sales']);
        Role::create(['name' => 'purchasing']);
        Role::create(['name' => 'finance']);
        Role::create(['name' => 'operator']);
        Role::create(['name' => 'supervisor']);
        Role::create(['name' => 'manager']);
    }
}
