<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'App\Http\Controllers', 'middleware' => ['guest']], function () {
    Route::get('login', ['uses' => 'AuthController@showLoginForm', 'as' => 'auth.show-login-form']);
    Route::post('login', ['uses' => 'AuthController@login', 'as' => 'auth.login']);
});

Route::group(['namespace' => 'App\Http\Controllers', 'middleware' => ['auth']], function () {
    Route::get('', ['uses' => 'App\Http\Controllers\HomeController@index', 'as' => 'home']);

    Route::post('logout', ['uses' => 'AuthController@logout', 'as' => 'auth.logout']);


    /* Application Settings */
    Route::group(['prefix' => 'application', 'as' => 'application.'], function () {
        Route::get('', ['uses' => 'ApplicationController@index', 'as' => 'index']);
        Route::get('edit', ['uses' => 'ApplicationController@edit', 'as' => 'edit']);
        Route::patch('', ['uses' => 'ApplicationController@update', 'as' => 'update']);
    });

    /* Company */
    Route::group(['prefix' => 'company', 'as' => 'company.', 'middleware' => ['permission:company.update']],
        function () {
            Route::get('', ['uses' => 'CompanyController@index', 'as' => 'index']);
            Route::get('edit', ['uses' => 'CompanyController@edit', 'as' => 'edit']);
            Route::patch('', ['uses' => 'CompanyController@update', 'as' => 'update']);
        });

    /* Roles and Permissions */
    Route::group(['prefix' => 'roles', 'as' => 'roles.', 'middleware' => ['permission:roles.update']], function () {
        Route::get('{role}/edit', ['uses' => 'RolesController@edit', 'as' => 'edit']);
    });

    /* Users */
    Route::group(['prefix' => 'users', 'as' => 'users.'], function () {
        Route::get('', [
            'uses' => 'UsersController@index',
            'as' => 'index',
        ])->can('viewAny', User::class);

        Route::delete('{user}', [
            'uses' => 'UsersController@destroy',
            'as' => 'destroy',
        ])->can('destroy', 'user');

        Route::patch('{user}/restore', [
            'uses' => 'UsersController@restore',
            'as' => 'restore',
        ])->withTrashed()->can('restore', 'user');

        Route::get('create', [
            'uses' => 'UsersController@create',
            'as' => 'create',
        ])->can('create', User::class);

        Route::get('{user}/edit', [
            'uses' => 'UsersController@edit',
            'as' => 'edit',
        ])->withTrashed()->can('update', 'user');

        /* Users Update */
        Route::group(['prefix' => 'update', 'as' => 'update.'], function () {

            Route::patch('employee-id/{user}', [
                'uses' => 'UsersUpdateController@updateEmployeeId',
                'as' => 'employee-id',
            ])->withTrashed()->can('updateEmployeeId', 'user');

            Route::patch('username/{user}', [
                'uses' => 'UsersUpdateController@updateUsername',
                'as' => 'username',
            ])->withTrashed()->can('updateUsername', 'user');

            Route::patch('company-profile/{user}', [
                'uses' => 'UsersUpdateController@updateCompanyProfile',
                'as' => 'company-profile',
            ])->withTrashed()->can('updateCompanyProfile', 'user');

            Route::patch('personal-profile/{user}', [
                'uses' => 'UsersUpdateController@updatePersonalProfile',
                'as' => 'personal-profile',
            ])->withTrashed()->can('updatePersonalProfile', 'user');

            Route::patch('address/{user}', [
                'uses' => 'UsersUpdateController@updateAddress',
                'as' => 'address',
            ])->withTrashed()->can('updateAddress', 'user');

            Route::patch('password/{user}', [
                'uses' => 'UsersUpdateController@updatePassword',
                'as' => 'password',
            ])->withTrashed();

            Route::patch('protected-info/{user}', [
                'uses' => 'UsersUpdateController@updateProtectedInfo',
                'as' => 'protected-info',
            ])->withTrashed()->can('updateProtectedInfo', User::class);

        });

        Route::post('check-username', [
            'uses' => 'UsersController@checkUsername',
            'as' => 'check-username',
        ])->can('updateUsername', User::class);

        Route::post('check-employee-id', [
            'uses' => 'UsersController@checkEmployeeId',
            'as' => 'check-employee-id',
        ])->can('updateEmployeeId', User::class);

        Route::post('', [
            'uses' => 'UsersController@store',
            'as' => 'store',
        ])->can('create', User::class);

        Route::post('get-users', [
            'uses' => 'UsersController@getUsers',
            'as' => 'get-users',
        ])->can('viewAny', User::class);
    });

    /* Sales Orders */
    Route::group(['prefix' => 'sales-orders', 'as' => 'sales-orders.'], function () {
        Route::get('', ['uses' => 'SalesOrdersController@index', 'as' => 'index']);
        Route::get('create', ['uses' => 'SalesOrdersController@create', 'as' => 'create']);
        Route::post('stock-bom-search', ['uses' => 'SalesOrdersController@stockBomSearch', 'as' => 'stock-bom-search']);
        Route::post('', ['uses' => 'SalesOrdersController@store', 'as' => 'store']);
    });

    /* Calendars */
    Route::group(['prefix' => 'calendars', 'as' => 'calendars.', 'middleware' => ['permission:calendars.update']],
        function () {
            Route::get('', ['uses' => 'CalendarsController@index', 'as' => 'index']);
            Route::get('create', ['uses' => 'CalendarsController@create', 'as' => 'create']);
            Route::get('{calendar}', ['uses' => 'CalendarsController@edit', 'as' => 'edit']);
            Route::patch('{calendar}', ['uses' => 'CalendarsController@update', 'as' => 'update']);

            /* Calendar Shifts */
            Route::group(['prefix' => 'calendar-shifts', 'as' => 'calendar-shifts.'], function () {
                Route::patch('edit/{calendar}', ['uses' => 'CalendarShiftsController@update', 'as' => 'update']);
                Route::post('{calendar}',
                    ['uses' => 'CalendarShiftsController@getCalendarShifts', 'as' => 'get-calendar-shifts']);
                Route::get('{calendar}', ['uses' => 'CalendarShiftsController@edit', 'as' => 'edit']);
            });
        });
});
