@extends('admin.layout')

@section('heading')
    All Calendars
@endsection

@section('breadcrumbs')
    {{ Breadcrumbs::render('admin.calendars.index') }}
@endsection

@section('content')
    <div id="content">
        <h3>Select Calendar</h3>

        <ul>
            @foreach ($calendars as $calendar)
                <li class="py-1 ml-6">
                    <a href="{{ route('admin.calendars.calendar-shifts.edit', $calendar->id) }}"
                       class="text-slate-500 hover:text-orange-500">{{ $calendar->name }}</a>
                </li>
            @endforeach
        </ul>
    </div>
@endsection
