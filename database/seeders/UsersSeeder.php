<?php

namespace Database\Seeders;

use App\Models\User;
use Intervention\Image\Laravel\Facades\Image as InterventionImage;
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

        $lee = User::create([
            'username' => 'lee',
            'password' => Hash::make('x'),
            'first_name' => 'Lee',
            'middle_names' => 'Craig',
            'last_name' => 'Jeffries',
            'full_name' => 'Lee Craig Jeffries',
            'employee_id' => 'E00002',
            'email' => 'leecjeffries@gmail.com',
            'active' => true,
            'joined_at' => '2024-01-01',
        ])->assignRole($role['admin']);

        $filePath = storage_path('app/images/user/profile/2/b0f8b49f22c718e9924f5b1165111a67.png');
        $img = null;

        if (file_exists($filePath)) {
            $img = InterventionImage::read($filePath);

            $img = [
                'filename' => 'b0f8b49f22c718e9924f5b1165111a67.png',
                'type' => 'user_profile',
                'hash' => hash_file('sha256', $filePath),
                'extension' => pathinfo($filePath, PATHINFO_EXTENSION),
                'mime_type' => mime_content_type($filePath),
                'alt_text' => 'Profile picture of Lee Jeffries',
                'comments' => 'Lee\'s profile picture',
                'size' => filesize($filePath),
                'width' => $img->width(),
                'height' => $img->height(),
                'default_image' => true,
                'imageable_id' => $lee->id,
                'imageable_type' => User::class,
            ];
        } else {
            $this->command->error("File not found: $filePath");
        }

        if ($img) {
            $lee->uploadedImages()->create($img);
        }

        User::create([
            'username' => 'jade',
            'password' => Hash::make('password'),
            'first_name' => 'Jade',
            'last_name' => 'Harvey',
            'full_name' => 'Jade Harvey',
            'employee_id' => 'E00003',
            'email' => 'jade@rivauk.co.uk',
            'active' => true,
            'joined_at' => '2024-01-01',
        ])->assignRole($role['admin']);

        $roleIds = Role::where('id', '!=', 1)->pluck('id')->toArray();

        User::factory(1000)->create()->each(function ($user) use ($roleIds) {
            $user->roles()->attach($roleIds[array_rand($roleIds)]);
        });
    }
}
