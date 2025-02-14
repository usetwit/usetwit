<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Symfony\Component\Intl\Countries;
use Tests\TestCase;

class UsersFeatureTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * A basic feature test example.
     */
    public function test_user_creation_with_address_and_role_saves_correctly_in_database(): void
    {
        $this->setUserWithPermissions('users.create');

        $role = Role::create(['name' => 'test-role-2']);
        $user = User::factory()->make();

        $country_code = $this->faker->countryCode;
        $country_name = Countries::getName($country_code);

        $payload = array_merge($user->toArray(), [
            'address_line_1' => $this->faker->streetAddress,
            'address_line_2' => $this->faker->city,
            'address_line_3' => $this->faker->city,
            'postcode' => $this->faker->postcode,
            'country_code' => $country_code,
            'country_name' => $country_name,
            'role_id' => $role->id,
            'joined_at' => optional($user->joined_at)->format('Y-m-d'),
            'password' => 'xiTh&£$5678HjnN',
            'password_confirmation' => 'xiTh&£$5678HjnN',
        ]);

        $response = $this->post(route('admin.users.store'), $payload);

        $response->assertStatus(200);

        $newUser = User::where('username', $payload['username'])->first();

        $this->assertTrue(Hash::check($payload['password'], $newUser->fresh()->password));

        $this->assertDatabaseHas('users', [
            'full_name' => $user->full_name,
            'active' => true,
            'username' => $payload['username'],
            'first_name' => $payload['first_name'],
            'middle_names' => $payload['middle_names'],
            'last_name' => $payload['last_name'],
            'company_number' => $payload['company_number'],
            'company_mobile_number' => $payload['company_mobile_number'],
            'company_ext' => $payload['company_ext'],
            'personal_number' => $payload['personal_number'],
            'personal_mobile_number' => $payload['personal_mobile_number'],
            'email' => $payload['email'],
            'employee_id' => $payload['employee_id'],
            'personal_email' => $payload['personal_email'],
            'emergency_name' => $payload['emergency_name'],
            'emergency_number' => $payload['emergency_number'],
            'joined_at' => $payload['joined_at'],
        ]);

        $this->assertDatabaseHas('addresses', [
            'address_line_1' => $payload['address_line_1'],
            'address_line_2' => $payload['address_line_2'],
            'address_line_3' => $payload['address_line_3'],
            'postcode' => $payload['postcode'],
            'country_code' => $payload['country_code'],
            'country_name' => $payload['country_name'],
            'default_address' => true,
            'addressable_type' => User::class,
            'addressable_id' => $newUser->id,
        ]);

        $this->assertDatabaseHas('model_has_roles', [
            'role_id' => $payload['role_id'],
            'model_type' => User::class,
            'model_id' => $newUser->id,
        ]);

        $this->assertDatabaseCount('users', 2);
        $this->assertDatabaseCount('addresses', 1);
        $this->assertDatabaseCount('model_has_roles', 2);
    }

    public function test_user_creation_inaccessible_to_user_without_permission(): void
    {
        $this->setUserWithPermissions();

        $response = $this->post(route('admin.users.store'));

        $response->assertStatus(403);
    }

    public function test_user_cannot_see_create_user_if_not_authenticated(): void
    {
        $response = $this->post(route('admin.users.store'));

        $response->assertStatus(302);
    }

    public function test_user_cannot_see_edit_user_if_not_authenticated(): void
    {
        $user = User::factory()->create();

        $response = $this->get(route('admin.users.edit', $user));

        $response->assertStatus(302);
    }

    public function test_admin_with_users_update_permission_user_can_see_edit_user(): void
    {
        $admin = $this->setUserWithPermissions('users.update');

        $user = User::factory()->create();

        $response = $this->get(route('admin.users.edit', $user));

        $response->assertStatus(200);
    }

    public function test_user_can_update_own_personal_profile(): void
    {
        $user = $this->setUserWithPermissions('users.update.self.personal-profile');

        $payload = [
            'first_name' => $this->faker->firstName,
            'middle_names' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'dob' => $this->faker->date('Y-m-d', '2001-01-01'),
            'personal_number' => $this->faker->phoneNumber,
            'personal_mobile_number' => $this->faker->phoneNumber,
            'personal_email' => $this->faker->email,
        ];

        $response = $this->patch(route('admin.users.update.personal-profile', $user), $payload);

        $response->assertStatus(200);

        $this->assertDatabaseHas('users', array_merge(['id' => $user->id], $payload));
    }

    public function test_admin_can_update_other_personal_profile(): void
    {
        $this->setUserWithPermissions('users.update');

        $user = User::factory()->create();

        $payload = [
            'first_name' => $this->faker->firstName,
            'middle_names' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'dob' => $this->faker->date('Y-m-d', '2001-01-01'),
            'personal_number' => $this->faker->phoneNumber,
            'personal_mobile_number' => $this->faker->phoneNumber,
            'personal_email' => $this->faker->email,
        ];

        $response = $this->patch(route('admin.users.update.personal-profile', $user), $payload);

        $response->assertStatus(200);

        $this->assertDatabaseHas('users', array_merge(['id' => $user->id], $payload));
    }

    public function test_admin_can_override_password(): void
    {
        $admin = $this->setUserWithPermissions('users.update');

        $currentPassword = $this->faker->password(20, 40);
        $newPassword = $this->faker->password(20, 40);

        $user = User::factory()->create(['password' => Hash::make($currentPassword)]);

        $payload = [
            'new_password' => $newPassword,
            'new_password_confirmation' => $newPassword,
        ];

        $response = $this->patch(route('admin.users.update.password', $user), $payload);

        $response->assertStatus(200);

        $this->assertTrue(Hash::check($payload['new_password'], $user->fresh()->password));
    }

    public function test_user_must_provide_current_password_when_updating_password(): void
    {
        $currentPassword = $this->faker->password(20, 40);
        $newPassword = $this->faker->password(20, 40);

        $user = User::factory()->create(['password' => Hash::make($currentPassword)]);
        $this->actingAs($user);

        $payload = [
            'new_password' => $newPassword,
            'new_password_confirmation' => $newPassword,
        ];

        $response = $this->patch(route('admin.users.update.password', $user), $payload);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['current_password' => 'The current password field is required.']);

        $payload = array_merge($payload, [
            'current_password' => $currentPassword,
        ]);

        $response = $this->patch(route('admin.users.update.password', $user), $payload);

        $response->assertStatus(200);

        $this->assertTrue(Hash::check($payload['new_password'], $user->fresh()->password));
    }
}
