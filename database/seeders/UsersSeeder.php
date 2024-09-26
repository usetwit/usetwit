<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role['system'] = Role::findByName('system');
        $role['admin'] = Role::findByName('admin');

        User::create([
            'username' => 'system',
            'first_name' => 'System',
            'password' => Hash::make('password'),
        ])->assignRole($role['system']);

        User::create([
            'username' => 'lee',
            'password' => Hash::make('x'),
            'first_name' => 'Lee',
            'middle_names' => 'Craig',
            'last_name' => 'Jeffries',
            'full_name' => 'Lee Craig Jeffries',
            'employee_id' => 'E00002',
            'email' => 'leecjeffries@gmail.com',
            'active' => true,
        ])->assignRole($role['admin']);

        User::create([
            'username' => 'jade',
            'password' => Hash::make('password'),
            'first_name' => 'Jade',
            'last_name' => 'Harvey',
            'full_name' => 'Jade Harvey',
            'employee_id' => 'E00003',
            'email' => 'jade@rivauk.co.uk',
            'active' => true,
        ])->assignRole($role['admin']);

        $roleIds = Role::where('id', '!=', 1)->pluck('id')->toArray();

        User::factory(50)->create()->each(function ($user) use ($roleIds) {
            $user->roles()->attach($roleIds[array_rand($roleIds)]);
        });
    }
}
