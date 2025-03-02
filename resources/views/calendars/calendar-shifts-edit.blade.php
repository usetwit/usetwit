@extends('admin.layout')

@section('heading')
    Edit Calendar: {{ $calendar->name }}
@endsection

@section('breadcrumbs')
    {{ Breadcrumbs::render('admin.calendars.calendar-shifts.edit', $calendar) }}
@endsection

@section('javascript')
    @parent

    @vite(['resources/js/app/calendar-shifts-edit.js'])
@endsection

@section('content')
    <div id="app">
        <calendar-shifts-edit route="{{ route('admin.calendars.calendar-shifts.get-calendar-shifts', $calendar) }}"
                         route-update="{{ route('admin.calendars.calendar-shifts.update', $calendar) }}"
                         :calendar-list="{{ Js::from($calendars) }}"
        ></calendar-shifts-edit>
    </div>
@endsection
