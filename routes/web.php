<?php

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
    Route::group(['prefix' => 'company', 'as' => 'company.', 'middleware' => ['permission:company.edit']], function () {
        Route::get('', ['uses' => 'CompanyController@index', 'as' => 'index']);
        Route::get('edit', ['uses' => 'CompanyController@edit', 'as' => 'edit']);
        Route::patch('', ['uses' => 'CompanyController@update', 'as' => 'update']);
    });

    /* Roles and Permissions */
    Route::group(['prefix' => 'roles', 'as' => 'roles.', 'middleware' => ['permission:roles.edit']], function () {
        Route::get('{role}/edit', ['uses' => 'RolesController@edit', 'as' => 'edit']);
    });

    /* Users */
    Route::group(['prefix' => 'users', 'as' => 'users.'], function () {
        Route::get('', ['uses' => 'UsersController@index', 'as' => 'index', 'middleware' => ['permission:users.edit']]);
        Route::get('create', ['uses' => 'UsersController@create', 'as' => 'create', 'middleware' => ['permission:users.create']]);
        Route::get('{user}/edit', ['uses' => 'UsersController@edit', 'as' => 'edit', 'middleware' => ['permission:users.edit']])->withTrashed();
        Route::patch('{user}', ['uses' => 'UsersController@update', 'as' => 'update', 'middleware' => ['permission:users.edit']])->withTrashed();
        Route::post('check-username', ['uses' => 'UsersController@checkUsername', 'as' => 'check-username', 'middleware' => ['permission:users.edit|users.create']]);
        Route::post('', ['uses' => 'UsersController@store', 'as' => 'store', 'middleware' => ['permission:users.create']]);
        Route::post('get-users', ['uses' => 'UsersController@getUsers', 'as' => 'get-users', 'middleware' => ['permission:users.create|users.edit']]);
    });

    /* Sales Orders */
    Route::group(['prefix' => 'sales-orders', 'as' => 'sales-orders.'], function () {
        Route::get('', ['uses' => 'SalesOrdersController@index', 'as' => 'index']);
        Route::get('create', ['uses' => 'SalesOrdersController@create', 'as' => 'create']);
        Route::post('stock-bom-search', ['uses' => 'SalesOrdersController@stockBomSearch', 'as' => 'stock-bom-search']);
        Route::post('', ['uses' => 'SalesOrdersController@store', 'as' => 'store']);
    });

    /* Calendars */
    Route::group(['prefix' => 'calendars', 'as' => 'calendars.', 'middleware' => ['permission:calendars.edit']], function () {
        Route::get('', ['uses' => 'CalendarsController@index', 'as' => 'index']);
        Route::get('create', ['uses' => 'CalendarsController@create', 'as' => 'create']);
        Route::get('{calendar}', ['uses' => 'CalendarsController@edit', 'as' => 'edit']);
        Route::patch('{calendar}', ['uses' => 'CalendarsController@update', 'as' => 'update']);

        /* Calendar Shifts */
        Route::group(['prefix' => 'calendar-shifts', 'as' => 'calendar-shifts.'], function () {
            Route::patch('edit/{calendar}', ['uses' => 'CalendarShiftsController@update', 'as' => 'update']);
            Route::post('{calendar}', ['uses' => 'CalendarShiftsController@getCalendarShifts', 'as' => 'get-calendar-shifts']);
            Route::get('{calendar}', ['uses' => 'CalendarShiftsController@edit', 'as' => 'edit']);
        });
    });
});
