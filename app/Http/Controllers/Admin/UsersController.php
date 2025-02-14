<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\CheckEmployeeIdRequest;
use App\Http\Requests\Users\CheckUsernameRequest;
use App\Http\Requests\Users\GetUsersRequest;
use App\Http\Requests\Users\StoreRequest;
use App\Models\User;
use App\Services\FilterService;
use App\Settings\GeneralSettings;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Symfony\Component\Intl\Countries;

class UsersController extends Controller
{
    public function index(GeneralSettings $settings)
    {
        $dateSettings = $settings->dateSettings();
        $paginationSettings = $settings->paginationSettings();

        $routeGetUsers = route('admin.users.get-users');

        return view('users.users-index', compact('paginationSettings', 'routeGetUsers', 'dateSettings'));
    }

    public function getUsers(GetUsersRequest $request, FilterService $service, GeneralSettings $settings)
    {
        $perPage = $request->input('per_page', $settings->per_page_default);
        $filters = $request->input('filters', []);
        $sorts = $request->input('sort', []);
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
            $cols = array_map(fn ($value) => 'users.'.$value, $cols);

            return array_merge($cols, ['roles.name as role_name']);
        });

        $query = DB::table('users')->select($cols)->leftJoin('model_has_roles', function ($join) {
            $join->on('model_has_roles.model_id', 'users.id')->where('model_has_roles.model_type', User::class);
        })->leftJoin('roles', 'roles.id', 'model_has_roles.role_id');

        $service->filterAndSort($query, $filters, $global, $visible, ['global'], $substitutions, $sorts);

        $query = $query->paginate($perPage);
        $total = $query->total();

        $users = $query->getCollection()->map(function ($user) {

            return array_merge((array) $user, [
                'edit_user_route' => route('admin.users.edit', $user->slug),
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
            'delete' => route('admin.users.destroy', $user),
            'restore' => route('admin.users.restore', $user),
            'protected_info' => route('admin.users.update.protected-info', $user),
            'address' => route('admin.users.update.address', $user),
            'personal_profile' => route('admin.users.update.personal-profile', $user),
            'company_profile' => route('admin.users.update.company-profile', $user),
            'password' => route('admin.users.update.password', $user),
            'username' => route('admin.users.update.username', $user),
            'employee_id' => route('admin.users.update.employee-id', $user),
            'check_employee_id' => route('admin.users.check-employee-id'),
            'check_username' => route('admin.users.check-username'),
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
        $routeCheckUsername = route('admin.users.check-username');
        $routeStore = route('admin.users.store');
        $routeRedirect = route('admin.users.index');
        $dateSettings = $settings->dateSettings();

        $maxIdPlusOne = User::max('id') + 1;
        $paddedId = str_pad($maxIdPlusOne, $settings->employee_id_padding, '0', STR_PAD_LEFT);
        $suggestedId = $settings->employee_id_prefix.$paddedId;
        $roles = Role::all(['id', 'name'])->map(function ($role) {
            $role->name = ucwords($role->name);

            return $role;
        });
        $selectedCountry = $settings->default_country;
        $countries = collect(Countries::getNames())->map(function (string $name, string $code) {
            return ['code' => $code, 'name' => $name];
        })->values();

        return view('users.users-create',
            compact('routeCheckUsername', 'routeStore', 'routeRedirect', 'dateSettings', 'suggestedId', 'roles',
                'countries', 'selectedCountry'));
    }

    public function checkUsername(CheckUsernameRequest $request)
    {
        $username = $request->input('username');

        if (! $username) {
            return [];
        }

        return User::withTrashed()->where('username', $username)->get(['username']);
    }

    public function checkEmployeeId(CheckEmployeeIdRequest $request)
    {
        $employee_id = $request->input('employee_id');

        if (! $employee_id) {
            return [];
        }

        return User::withTrashed()->where('employee_id', $employee_id)->get(['employee_id']);
    }

    public function store(StoreRequest $request)
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

        $addressFields = $request->only(['address_line_1', 'address_line_2', 'address_line_3', 'postcode', 'country_code']);

        if (count(Arr::whereNotNull($addressFields)) > 0) {
            $addressFields['default_address'] = true;
            $newUser->address()->create($addressFields);
        }

        $role = Role::find($request->input('role_id'));
        $newUser->syncRoles($role);

        return ['message' => 'User Created', 'redirect' => route('admin.users.edit', $newUser)];
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->back()->with('success', 'User deleted');
    }

    public function restore(User $user)
    {
        $user->restore();

        return redirect()->back()->with('success', 'User restored');
    }
}
