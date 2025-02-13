<?php

use App\Models\Location;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::namespace('App\Http\Controllers')->middleware('guest')->group(function () {
    Route::get('login', 'AuthController@showLoginForm')->name('auth.show-login-form');
    Route::post('login', 'AuthController@login')->name('auth.login');
});

Route::namespace('App\Http\Controllers')->middleware('auth')->group(function () {
    Route::post('logout', 'AuthController@logout')->name('auth.logout');
});

/* Admin */
Route::prefix('admin')->name('admin.')->namespace('App\Http\Controllers\Admin')->middleware('auth')->group(function () {
    Route::get('', 'HomeController@index')->name('home');

    /* Application Settings */
    Route::prefix('application')->name('application.')->controller('ApplicationController')->group(function () {
        Route::get('', 'index')->name('index');
        Route::get('edit', 'edit')->name('edit');
        Route::patch('', 'update')->name('update');
    });

    /* Company */
    Route::prefix('company')->name('company.')->middleware('permission:company.update')->controller('CompanyController')->group(function () {
        Route::get('edit', 'edit')->name('edit');
        Route::patch('', 'update')->name('update');
    });

//    /* Roles and Permissions */
//    Route::prefix('roles')->name('roles.')->middleware('permission:roles.update')->controller('RolesController')->group(function () {
//        Route::get('{role}/edit', 'edit')->name('edit');
//    });

    /* Users */
    Route::prefix('users')->name('users.')->controller('UsersController')->group(function () {
        Route::get('', 'index')->name('index')->can('viewAny', User::class);
        Route::delete('{user}', 'destroy')->name('destroy')->can('delete', 'user');
        Route::patch('{user}/restore', 'restore')->name('restore')->withTrashed()->can('restore', 'user');
        Route::get('create', 'create')->name('create')->can('create', User::class);
        Route::get('{user}/edit', 'edit')->name('edit')->withTrashed()->can('update', 'user');

        /* Users Update */
        Route::prefix('update')->name('update.')->controller('UsersUpdateController')->group(function () {
            Route::patch('employee-id/{user}', 'updateEmployeeId')->name('employee-id')->withTrashed()->can('updateEmployeeId', 'user');
            Route::patch('username/{user}', 'updateUsername')->name('username')->withTrashed()->can('updateUsername', 'user');
            Route::patch('company-profile/{user}', 'updateCompanyProfile')->name('company-profile')->withTrashed()->can('updateCompanyProfile', 'user');
            Route::patch('personal-profile/{user}', 'updatePersonalProfile')->name('personal-profile')->withTrashed()->can('updatePersonalProfile', 'user');
            Route::patch('address/{user}', 'updateAddress')->name('address')->withTrashed()->can('updateAddress', 'user');
            Route::patch('password/{user}', 'updatePassword')->name('password')->withTrashed();
            Route::patch('protected-info/{user}', 'updateProtectedInfo')->name('protected-info')->withTrashed()->can('updateProtectedInfo', User::class);
        });

        Route::post('check-username', 'checkUsername')->name('check-username')->can('updateUsername', User::class);
        Route::post('check-employee-id', 'checkEmployeeId')->name('check-employee-id')->can('updateEmployeeId', User::class);
        Route::post('', 'store')->name('store')->can('create', User::class);
        Route::post('get-users', 'getUsers')->name('get-users')->can('viewAny', User::class);
    });

    /* Sales Orders */
    Route::prefix('sales-orders')->name('sales-orders.')->controller('SalesOrdersController')->group(function () {
        Route::get('', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('stock-bom-search', 'stockBomSearch')->name('stock-bom-search');
        Route::post('', 'store')->name('store');
    });

    /* Calendars */
    Route::prefix('calendars')->name('calendars.')->middleware('permission:calendars.update')->controller('CalendarsController')->group(function () {
        Route::get('', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::get('{calendar}', 'edit')->name('edit');
        Route::patch('{calendar}', 'update')->name('update');

        /* Calendar Shifts */
        Route::prefix('calendar-shifts')->name('calendar-shifts.')->controller('CalendarShiftsController')->group(function () {
            Route::patch('edit/{calendar}', 'update')->name('update');
            Route::post('{calendar}', 'getCalendarShifts')->name('get-calendar-shifts');
            Route::get('{calendar}', 'edit')->name('edit');
        });
    });

    /* Locations */
    Route::prefix('locations')->name('locations.')->controller('LocationsController')->group(function () {
        Route::get('', 'index')->name('index')->can('viewAny', Location::class);
        Route::get('create', 'create')->name('create')->can('create', Location::class);
        Route::patch('edit/{location}', 'update')->name('update')->can('update', Location::class);
        Route::post('', 'getLocations')->name('get-locations')->can('viewAny', Location::class);
        Route::get('{location}', 'edit')->name('edit')->can('update', Location::class);
    });
});
