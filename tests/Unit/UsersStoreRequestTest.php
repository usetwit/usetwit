<?php

namespace Tests\Unit;

use App\Http\Requests\Users\UsersStoreRequest;
use App\Models\User;
use App\Rules\HasMultipleConstraints;
use App\Rules\PasswordStrength;
use App\Settings\GeneralSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\MockObject\Exception;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UsersStoreRequestTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function sampleData(array $overrides = []): array
    {
        $defaultData = User::factory()->make()->toArray();
        $defaultData['password'] = 'password';
        $defaultData['password_confirmation'] = 'password';
        $defaultData['role_id'] = 1;
        $defaultData['address_line_1'] = $this->faker->streetAddress;
        $defaultData['address_line_2'] = $this->faker->city;
        $defaultData['address_line_3'] = $this->faker->city;
        $defaultData['postcode'] = $this->faker->postcode;
        $defaultData['country'] = 'gb';

        return array_merge($defaultData, $overrides);
    }

    protected function validate(array $data)
    {
        $request = new UsersStoreRequest();
        return Validator::make($data, $request->rules());
    }

    /**
     * @throws Exception
     */
    public function test_password_must_meet_required_strength()
    {
        $settings = $this->createMock(GeneralSettings::class);
        $settings->password_strength = 3;

        $this->app->instance(GeneralSettings::class, $settings);

        $rule = new PasswordStrength();
        $errorMessage = '';
        $fail = $this->failClosure($errorMessage);

        $password = 'a';
        $rule->validate('password', $password, $fail);

        $this->assertEquals('The :attribute field is not a strong enough password.', $errorMessage);
    }

    /**
     * @throws Exception
     */
    public function test_password_meets_required_strength()
    {
        $settings = $this->createMock(GeneralSettings::class);
        $settings->password_strength = 3;

        $this->app->instance(GeneralSettings::class, $settings);

        $rule = new PasswordStrength();
        $errorMessage = '';
        $fail = $this->failClosure($errorMessage);

        $password = 'aHtY&^39LOhg';
        $rule->validate('password', $password, $fail);

        $this->assertEmpty($errorMessage);
    }

    public function test_valid_role_id()
    {
        $role = Role::create(['name' => 'test-role']);

        $validator = $this->validate($this->sampleData([
            'role_id' => $role->id,
        ]));

        $this->assertFalse($validator->errors()->has('role_id'));
    }

    public function test_invalid_role_id()
    {
        $validator = $this->validate($this->sampleData([
            'role_id' => 999,
        ]));

        $this->assertTrue($validator->errors()->has('role_id'));
    }

    public function test_missing_role_id()
    {
        $validator = $this->validate($this->sampleData([]));

        $this->assertTrue($validator->errors()->has('role_id'));
    }

    public function test_role_id_is_integer()
    {
        $validator = $this->validate($this->sampleData([
            'role_id' => 'string',
        ]));

        $this->assertTrue($validator->errors()->has('role_id'));
    }

    public function test_role_id_is_not_integer()
    {
        $validator = $this->validate($this->sampleData([
            'role_id' => 12.34,
        ]));

        $this->assertTrue($validator->errors()->has('role_id'));
    }

    public function test_employee_id_must_be_unique()
    {
        User::factory()->create(['employee_id' => '12345']);

        $validator = $this->validate($this->sampleData(['employee_id' => '12345']));
        $this->assertTrue($validator->errors()->has('employee_id'));
    }

    public function test_username_contains_only_lowercase_letters_and_numbers()
    {
        $validUsernames = [
            'validusername1',
            'anotheruser123',
            'user2024',
        ];

        foreach ($validUsernames as $username) {
            $validator = $this->validate($this->sampleData(['username' => $username]));
            $this->assertFalse($validator->errors()->has('username'), "Failed for username: $username");
        }

        $invalidUsernames = [
            'InvalidUsername',
            'user!@#$',
            'user name',
            'user_name',
        ];

        foreach ($invalidUsernames as $username) {
            $validator = $this->validate($this->sampleData(['username' => $username]));
            $this->assertTrue($validator->errors()->has('username'), "Failed for username: $username");
        }
    }

    public function test_username_is_required()
    {
        $validator = $this->validate($this->sampleData(['username' => '']));
        $this->assertTrue($validator->errors()->has('username'));
    }

    public function test_first_name_is_required()
    {
        $validator = $this->validate($this->sampleData(['first_name' => '']));
        $this->assertTrue($validator->errors()->has('first_name'));
    }

    public function test_username_must_be_unique()
    {
        User::factory()->create(['username' => 'existinguser']);

        $validator = $this->validate($this->sampleData(['username' => 'existinguser']));
        $this->assertTrue($validator->errors()->has('username'));
    }

    public function test_username_max_length()
    {
        $validator = $this->validate($this->sampleData(['username' => $this->longString()]));
        $this->assertTrue($validator->errors()->has('username'));
    }

    public function test_first_name_max_length()
    {
        $validator = $this->validate($this->sampleData(['first_name' => $this->longString(86)]));
        $this->assertTrue($validator->errors()->has('first_name'));

        $validator = $this->validate($this->sampleData(['first_name' => $this->longString(85)]));
        $this->assertFalse($validator->errors()->has('first_name'));
    }

    public function test_middle_names_max_length()
    {
        $validator = $this->validate($this->sampleData(['middle_names' => $this->longString(86)]));
        $this->assertTrue($validator->errors()->has('middle_names'));

        $validator = $this->validate($this->sampleData(['middle_names' => $this->longString(85)]));
        $this->assertFalse($validator->errors()->has('middle_names'));
    }

    public function test_last_name_max_length()
    {
        $validator = $this->validate($this->sampleData(['last_name' => $this->longString(86)]));
        $this->assertTrue($validator->errors()->has('last_name'));

        $validator = $this->validate($this->sampleData(['last_name' => $this->longString(85)]));
        $this->assertFalse($validator->errors()->has('last_name'));
    }

    public function test_password_is_required()
    {
        $validator = $this->validate($this->sampleData(['password' => '', 'password_confirmation' => '']));
        $this->assertTrue($validator->errors()->has('password'));
    }

    public function test_password_confirmation()
    {
        $validator = $this->validate($this->sampleData(['password_confirmation' => 'different_password']));
        $this->assertTrue($validator->errors()->has('password'));
    }

    public function test_password_max_length()
    {
        $validator = $this->validate($this->sampleData([
            'password' => $this->longString(),
            'password_confirmation' => $this->longString(),
        ]));
        $this->assertTrue($validator->errors()->has('password'));
    }

    public function test_email_and_home_email_format()
    {
        $fields = ['email', 'personal_email'];

        $validEmails = [
            'user@example.com',
            'user.name@example.co.uk',
            'user_name@example.io',
            'user+name@example.com',
        ];

        $invalidEmails = [
            'user@example',
            'user@.com',
            'user@-example.com',
            'user@example.com.',
            'user@.com',
            'user@example..com',
        ];

        foreach ($fields as $field) {
            foreach ($validEmails as $email) {
                $validator = $this->validate($this->sampleData([$field => $email]));
                $this->assertFalse($validator->errors()->has($field), "Failed for $field with email: $email");
            }

            foreach ($invalidEmails as $email) {
                $validator = $this->validate($this->sampleData([$field => $email]));
                $this->assertTrue($validator->errors()->has($field), "Failed for $field with email: $email");
            }
        }
    }

    public function test_telephone_number_formats()
    {
        $fields = [
            'company_number',
            'company_mobile_number',
            'personal_number',
            'personal_mobile_number',
            'emergency_number',
        ];

        $validNumbers = ['1234567890', '123 456 7890', '+123 (456) 7890', '123-456-7890', '123.456.789'];
        $invalidNumbers = ['123ABC456', $this->longString(256, true)];

        foreach ($fields as $field) {
            foreach ($validNumbers as $number) {
                $validator = $this->validate($this->sampleData([$field => $number]));
                $this->assertFalse($validator->errors()->has($field), "Failed for $field with number: $number");
            }

            foreach ($invalidNumbers as $number) {
                $validator = $this->validate($this->sampleData([$field => $number]));
                $this->assertTrue($validator->errors()->has($field), "Failed for $field with number: $number");
            }
        }
    }

    public function test_company_ext_format()
    {
        $validNumbers = ['1234567890', '123 456 7890'];
        $invalidNumbers = ['123ABC456', '123-456-7890', $this->longString(256, true)];

        foreach ($validNumbers as $number) {
            $validator = $this->validate($this->sampleData(['company_ext' => $number]));
            $this->assertFalse($validator->errors()->has('company_ext'), "Failed for company_ext with number: $number");
        }

        foreach ($invalidNumbers as $number) {
            $validator = $this->validate($this->sampleData(['company_ext' => $number]));
            $this->assertTrue($validator->errors()->has('company_ext'), "Failed for company_ext with number: $number");
        }
    }
}
