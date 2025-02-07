@extends('app.layout')

@section('heading')
    Edit Calendar
@endsection

@section('breadcrumbs')
    {{ Breadcrumbs::render('calendars.index') }}
@endsection

@section('content')
    <div id="content">
        <h3>Select Calendar</h3>

        <ul>
            @foreach ($calendars as $calendar)
                <li class="py-1 ml-2">
                    <a href="{{ route('calendars.calendar-shifts.edit', $calendar) }}"
                       class="text-slate-500 hover:text-orange-500">{{ $calendar->name }}</a>
                </li>
            @endforeach
        </ul>
    </div>
@endsection
