<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\UsersCreateCheckUsernameRequest;
use App\Http\Requests\Users\UsersIndexGetUsersRequest;
use App\Http\Requests\Users\UsersStoreRequest;
use App\Models\User;
use App\Services\FilterService;
use App\Settings\GeneralSettings;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

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
        })->leftJoin('roles', 'roles.id', 'model_has_roles.role_id')->where('users.id', '!=', 1);

        $service->globalFilter($query, $filters['global']['constraints'][0]['value'], $global, $visible, $substitutions)
                ->filter($query, $filters, ['global'], $substitutions)->sort($query, $sort, ['global'], $substitutions);

        $query = $query->paginate($perPage);
        $total = $query->total();

        $users = $query->getCollection()->map(function ($user) {
            return array_merge((array) $user, [
                'edit_user_route' => route('users.edit', $user->id),
                'created_at' => Carbon::parse($user->created_at)->format('Y-m-d'),
                'updated_at' => Carbon::parse($user->updated_at)->format('Y-m-d'),
                'joined_at' => $user->joined_at === null ? null : Carbon::parse($user->joined_at)->format('Y-m-d'),
            ]);
        });

        return compact('users', 'total');
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
                'countries', 'selectedCountry'));
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
            'joined_at',
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
