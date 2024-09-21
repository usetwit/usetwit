<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\UsersCreateCheckUsernameRequest;
use App\Http\Requests\Users\UsersIndexGetUsersRequest;
use App\Http\Requests\Users\UsersStoreRequest;
use App\Models\User;
use App\Services\FilterService;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

class UsersController extends Controller
{
    public function index(GeneralSettings $settings)
    {
        $defaultPerPage = $settings->users_index_per_page;
        $perPageOptions = $settings->users_index_per_page_options;
        $routeGetUsers = route('users.get-users');

        return view('users.users-index', compact('defaultPerPage', 'perPageOptions', 'routeGetUsers'));
    }

    public function getUsers(UsersIndexGetUsersRequest $request, FilterService $service)
    {
        $perPage = $request->input('per_page');
        $filters = $request->input('filters');
        $sort = $request->input('sort');

        $cols = Schema::getColumnListing('users');
        $cols = array_diff($cols, ['password', 'remember_token']);
        $cols = array_map(fn($value) => 'users.' . $value, $cols);
        $cols = array_merge($cols, ['roles.name as role_name']);

        $query = User::withTrashed()->select($cols)->leftJoin('model_has_roles', function ($join) {
            $join->on('model_has_roles.model_id', 'users.id')->where('model_has_roles.model_type', User::class);
        })->leftJoin('roles', 'roles.id', 'model_has_roles.role_id');

        $service->filter($query, $filters, ['global'], ['role' => 'roles.name']);
        $service->sort($query, $sort, ['global'], ['role' => 'roles.name']);

        $query = $query->paginate($perPage);
        $total = $query->total();

        $users = $query->map(function ($user) {
            return array_merge($user->toArray(), [
                'edit_user_route' => route('users.edit', $user),
            ]);
        });

        $roles = Role::all(['name'])->pluck('name')->reject(fn($role) => $role === 'system');

        return compact('users', 'roles', 'total');
    }


    public function edit(User $user)
    {
        return $user;
    }

    public function create(GeneralSettings $settings)
    {
        $routeCheckUsername = route('users.check-username');
        $routeStore = route('users.store');
        $routeRedirect = route('users.index');
        $dateFormat = $settings->date_format_input;

        $maxIdPlusOne = User::max('id') + 1;
        $paddedId = str_pad($maxIdPlusOne, $settings->employee_id_padding, '0', STR_PAD_LEFT);
        $suggestedId = $settings->employee_id_prefix . $paddedId;
        $roles = Role::all(['id', 'name']);
        $countries = $settings->countriesAsArrayForJson();
        $selectedCountry = $settings->countryAsObjectForJson();

        return view('users.users-create',
            compact('routeCheckUsername', 'routeStore', 'routeRedirect', 'dateFormat', 'suggestedId', 'roles',
                'countries', 'selectedCountry',));
    }

    public function checkUsername(UsersCreateCheckUsernameRequest $request)
    {
        $username = $request->input('username');

        if (!$username) {
            return [];
        }

        return User::withTrashed()->where('username', $username)->get(['username']);
    }

    public function store(UsersStoreRequest $request)
    {
        $userFields = $request->only([
            'username',
            'employee_id',
            'password',
            'first_name',
            'middle_names',
            'last_name',
            'company_number',
            'company_ext',
            'home_number',
            'mobile_number',
            'emergency_name',
            'emergency_number',
            'email',
            'home_email',
            'join_date',
        ]);

        $userFields['password'] = Hash::make($userFields['password']);
        $newUser = User::create($userFields);

        $addressFields = $request->only(['address_line_1', 'address_line_2', 'address_line_3', 'postcode', 'country',]);

        $addressFields['default_address'] = true;
        $newUser->addresses()->create($addressFields);

        $role = Role::find($request->input('role_id'));
        $newUser->syncRoles($role);

        return 'User Created';
    }
}
