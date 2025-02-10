<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\UpdateAddressRequest;
use App\Http\Requests\Users\UpdateCompanyProfileRequest;
use App\Http\Requests\Users\UpdateEmployeeIdRequest;
use App\Http\Requests\Users\UpdatePasswordRequest;
use App\Http\Requests\Users\UpdatePersonalProfileRequest;
use App\Http\Requests\Users\UpdateProtectedInfoRequest;
use App\Http\Requests\Users\UpdateUsernameRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersUpdateController extends Controller
{
    /**
     * @param  UpdatePasswordRequest  $request
     * @param  User  $user
     *
     * @return string
     */
    public function updatePassword(UpdatePasswordRequest $request, User $user)
    {
        $user->update(['password' => Hash::make($request->input('new_password'))]);

        return 'Password updated successfully';
    }

    /**
     * @param  UpdatePersonalProfileRequest  $request
     * @param  User  $user
     *
     * @return string
     */
    public function updatePersonalProfile(UpdatePersonalProfileRequest $request, User $user)
    {
        $user->update($request->only([
            'first_name',
            'middle_names',
            'last_name',
            'dob',
            'personal_number',
            'personal_mobile_number',
            'personal_email',
        ]));

        return 'Personal profile updated successfully';
    }

    /**
     * @param  UpdateCompanyProfileRequest  $request
     * @param  User  $user
     *
     * @return string
     */
    public function updateCompanyProfile(UpdateCompanyProfileRequest $request, User $user)
    {
        $user->update($request->only([
            'email',
            'company_number',
            'company_ext',
            'company_mobile_number',
        ]));

        return 'Company profile updated successfully';
    }

    /**
     * @param  UpdateProtectedInfoRequest  $request
     * @param  User  $user
     *
     * @return string
     */
    public function updateProtectedInfo(UpdateProtectedInfoRequest $request, User $user)
    {
        $user->update($request->only([
            'username',
            'joined_at',
            'left_at',
            'employee_id',
        ]));

        $user->syncRoles($request->input('role_id'));

        return 'Protected info updated successfully';
    }

    /**
     * @param  UpdateAddressRequest  $request
     * @param  User  $user
     *
     * @return string
     */
    public function updateAddress(UpdateAddressRequest $request, User $user)
    {
        $addressFields = $request->only(['address_line_1', 'address_line_2', 'address_line_3', 'postcode', 'country']);

        $addressFields['default_address'] = true;

        if ($user->address()->first()) {
            $user->address()->update($addressFields);
        } else {
            $user->address()->create($addressFields);
        }

        return 'Address updated successfully';
    }

    /**
     * @param  UpdateUsernameRequest  $request
     * @param  User  $user
     *
     * @return string
     */
    public function updateUsername(UpdateUsernameRequest $request, User $user)
    {
        $user->update(['username' => $request->input('username')]);

        return 'Username updated successfully';
    }

    /**
     * @param  UpdateEmployeeIdRequest  $request
     * @param  User  $user
     *
     * @return string
     */
    public function updateEmployeeId(UpdateEmployeeIdRequest $request, User $user)
    {
        $user->update(['employee_id' => $request->input('employee_id')]);

        return 'Employee ID updated successfully';
    }
}
