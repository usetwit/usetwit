<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Home
Breadcrumbs::for('home', function (BreadcrumbTrail $trail) {
    $trail->push('Home', 'home');
});

/* Auth */
Breadcrumbs::for('auth.show-login-form', function (BreadcrumbTrail $trail) {
    $trail->push('Sign In');
});

/* Sales Orders */
Breadcrumbs::for('sales-orders.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Sales Orders', route('sales-orders.index'));
});

Breadcrumbs::for('sales-orders.create', function (BreadcrumbTrail $trail) {
    $trail->parent('sales-orders.index');
    $trail->push('Create New Sales Order');
});

/* Calendars */
Breadcrumbs::for('calendars.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Calendars', route('calendars.index'));
});

Breadcrumbs::for('calendars.show', function (BreadcrumbTrail $trail, $calendar) {
    $trail->parent('calendars.index');
    $trail->push($calendar->name);
});

Breadcrumbs::for('calendars.calendar-shifts.edit', function (BreadcrumbTrail $trail, $calendar) {
    $trail->parent('calendars.index');
    $trail->push($calendar->name);
});

/* Users */
Breadcrumbs::for('users.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('All Users', route('users.index'));
});

Breadcrumbs::for('users.create', function (BreadcrumbTrail $trail) {
    $trail->parent('users.index');
    $trail->push('Create New User');
});


/* Company */
Breadcrumbs::for('company.edit', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Company', route('company.edit'));
});
