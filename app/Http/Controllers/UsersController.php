<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\UsersCheckEmployeeIdRequest;
use App\Http\Requests\Users\UsersCheckUsernameRequest;
use App\Http\Requests\Users\UsersIndexGetUsersRequest;
use App\Http\Requests\Users\UsersStoreRequest;
use App\Models\User;
use App\Services\FilterService;
use App\Settings\GeneralSettings;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Symfony\Component\Intl\Countries;
use Illuminate\Database\Eloquent\Builder;
use Intervention\Image\Laravel\Facades\Image as InterventionImage;

class UsersController extends Controller
{
    public function index(GeneralSettings $settings)
    {
        $dateSettings = $settings->dateSettings();
        $paginationSettings = $settings->paginationSettings();

        $routeGetUsers = route('users.get-users');

        return view('users.users-index', compact('paginationSettings', 'routeGetUsers', 'dateSettings'));
    }

    public function getUsers(UsersIndexGetUsersRequest $request, FilterService $service, GeneralSettings $settings)
    {
        $perPage = $request->input('per_page', $settings->per_page_default);
        $filters = $request->input('filters', []);
        $sort = $request->input('sort', []);
        $visible = $request->input('visible', []);

        $substitutions = ['role_name' => 'roles.name', 'id' => 'users.id'];
        $global = [
            'username',
            'email',
            'first_name',
            'middle_names',
            'last_name',
            'full_name',
            'employee_id',
            'roles.name',
            'users.id',
        ];

        $cols = Cache::remember('user_columns', 24 * 60 * 60 * 7, function () {
            $cols = Schema::getColumnListing('users');
            $cols = array_diff($cols, ['password', 'remember_token']);
            $cols = array_map(fn($value) => 'users.' . $value, $cols);

            return array_merge($cols, ['roles.name as role_name']);
        });

        $query = DB::table('users')->select($cols)->leftJoin('model_has_roles', function ($join) {
            $join->on('model_has_roles.model_id', 'users.id')->where('model_has_roles.model_type', User::class);
        })->leftJoin('roles', 'roles.id', 'model_has_roles.role_id');

        $service->globalFilter($query, $filters['global']['constraints'][0]['value'], $global, $visible, $substitutions)
                ->filter($query, $filters, ['global'], $substitutions)->sort($query, $sort, ['global'], $substitutions);

        $query = $query->paginate($perPage);
        $total = $query->total();

        $users = $query->getCollection()->map(function ($user) {

            return array_merge((array) $user, [
                'edit_user_route' => route('users.edit', $user->slug),
                'created_at' => Carbon::parse($user->created_at)->format('Y-m-d'),
                'updated_at' => Carbon::parse($user->updated_at)->format('Y-m-d'),
                'joined_at' => $user->joined_at === null ? null : Carbon::parse($user->joined_at)->format('Y-m-d'),
            ]);
        });

        return compact('users', 'total');
    }

    public function edit(User $user, GeneralSettings $settings)
    {
        $dateSettings = $settings->dateSettings();

        $permissions = [
            'protected_info' => auth()->user()->can('updateProtectedInfo', User::class),
            'delete' => auth()->user()->can('delete', User::class),
            'restore' => auth()->user()->can('restore', User::class),
            'address' => auth()->user()->can('updateAddress', $user),
            'personal_profile' => auth()->user()->can('updatePersonalProfile', $user),
            'company_profile' => auth()->user()->can('updateCompanyProfile', $user),
            'image' => auth()->user()->can('updateProfileImage', $user),
            'override_password' => auth()->user()->can('overridePassword', User::class),
            'username' => auth()->user()->can('updateUsername', User::class),
            'employee_id' => auth()->user()->can('updateEmployeeId', User::class),
        ];

        $routes = [
            'delete' => route('users.destroy', $user),
            'restore' => route('users.restore', $user),
            'protected_info' => route('users.update.protected-info', $user),
            'address' => route('users.update.address', $user),
            'personal_profile' => route('users.update.personal-profile', $user),
            'company_profile' => route('users.update.company-profile', $user),
            'password' => route('users.update.password', $user),
            'username' => route('users.update.username', $user),
            'employee_id' => route('users.update.employee-id', $user),
            'check_employee_id' => route('users.check-employee-id'),
            'check_username'=>route('users.check-username'),
        ];

        $countries = collect(Countries::getNames())->map(function (string $name, string $code) {
            return ['code' => $code, 'name' => $name];
        })->values();

        $user->load([
            'address',
            'profileImages',
            'roles' => function (MorphToMany $query) {
                $query->limit(1);
            },
        ]);

        $roles = Role::get(['id', 'name']);

        return view('users.users-edit', compact('user', 'routes', 'roles', 'permissions', 'countries', 'dateSettings'));
    }

    public function create(GeneralSettings $settings)
    {
        $routeCheckUsername = route('users.check-username');
        $routeStore = route('users.store');
        $routeRedirect = route('users.index');
        $dateSettings = $settings->dateSettings();

        $maxIdPlusOne = User::max('id') + 1;
        $paddedId = str_pad($maxIdPlusOne, $settings->employee_id_padding, '0', STR_PAD_LEFT);
        $suggestedId = $settings->employee_id_prefix . $paddedId;
        $roles = Role::all(['id', 'name']);
        $selectedCountry = $settings->default_country;
        $countries = collect(Countries::getNames())->map(function (string $name, string $code) {
            return ['code' => $code, 'name' => $name];
        })->values();

        return view('users.users-create',
            compact('routeCheckUsername', 'routeStore', 'routeRedirect', 'dateSettings', 'suggestedId', 'roles',
                'countries', 'selectedCountry'));
    }

    public function checkUsername(UsersCheckUsernameRequest $request)
    {
        $username = $request->input('username');

        if (!$username) {
            return [];
        }

        return User::withTrashed()->where('username', $username)->get(['username']);
    }

    public function checkEmployeeId(UsersCheckEmployeeIdRequest $request)
    {
        $employee_id = $request->input('employee_id');

        if (!$employee_id) {
            return [];
        }

        return User::withTrashed()->where('employee_id', $employee_id)->get(['employee_id']);
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
            'company_mobile_number',
            'company_ext',
            'personal_number',
            'personal_mobile_number',
            'emergency_name',
            'emergency_number',
            'email',
            'personal_email',
            'joined_at',
        ]);

        $userFields['password'] = Hash::make($userFields['password']);
        $newUser = User::create($userFields);

        $addressFields = $request->only(['address_line_1', 'address_line_2', 'address_line_3', 'postcode', 'country']);

        if (count(Arr::whereNotNull($addressFields)) > 0) {
            $addressFields['default_address'] = true;
            $newUser->address()->create($addressFields);
        }

        $role = Role::find($request->input('role_id'));
        $newUser->syncRoles($role);

        return ['message' => 'User Created', 'redirect' => route('users.edit', $newUser)];
    }
}
