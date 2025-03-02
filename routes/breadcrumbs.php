<?php

use App\Models\User;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Home
Breadcrumbs::for('admin.home', function (BreadcrumbTrail $trail) {
    $trail->push('Home', 'home');
});

/* Auth */
Breadcrumbs::for('auth.show-login-form', function (BreadcrumbTrail $trail) {
    $trail->push('Sign In');
});

/* Sales Orders */
Breadcrumbs::for('admin.sales-orders.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Sales Orders', route('admin.sales-orders.index'));
});

Breadcrumbs::for('admin.sales-orders.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.sales-orders.index');
    $trail->push('Create New Sales Order');
});

/* Calendars */
Breadcrumbs::for('admin.calendars.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Calendars', route('admin.calendars.index'));
});

Breadcrumbs::for('admin.calendars.show', function (BreadcrumbTrail $trail, $calendar) {
    $trail->parent('admin.calendars.index');
    $trail->push($calendar->name);
});

Breadcrumbs::for('admin.calendars.calendar-shifts.edit', function (BreadcrumbTrail $trail, $calendar) {
    $trail->parent('admin.calendars.index');
    $trail->push($calendar->name);
});

/* Users */
Breadcrumbs::for('admin.users.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('All Users', route('admin.users.index'));
});

Breadcrumbs::for('admin.users.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.users.index');
    $trail->push('Create New User');
});

Breadcrumbs::for('admin.users.edit', function (BreadcrumbTrail $trail, User $user) {
    $trail->parent('admin.users.index');
    $trail->push('Edit User: ' . $user->full_name);
});

/* Company */
Breadcrumbs::for('admin.company.edit', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.home');
    $trail->push('Company', route('admin.company.edit'));
});
