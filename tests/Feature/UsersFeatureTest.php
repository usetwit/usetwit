<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
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

        $payload = array_merge($user->toArray(), [
            'address_line_1' => $this->faker->streetAddress,
            'address_line_2' => $this->faker->city,
            'address_line_3' => $this->faker->city,
            'postcode' => $this->faker->postcode,
            'country' => $this->faker->countryCode,
            'role_id' => $role->id,
            'joined_at' => optional($user->joined_at)->format('Y-m-d'),
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response = $this->post(route('users.store'), $payload);

        $response->assertStatus(200);

        $newUser = User::where('username', $payload['username'])->first();

        $this->assertTrue(Hash::check($payload['password'], $newUser->password));

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
            'country' => $payload['country'],
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

        $response = $this->post(route('users.store'));

        $response->assertStatus(403);
    }

    public function test_user_cannot_see_create_user_if_not_authenticated(): void
    {
        $response = $this->post(route('users.store'));

        $response->assertStatus(302);
    }
}
