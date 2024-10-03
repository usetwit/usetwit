@extends('app.layout')

@section('heading')
    Edit Calendar
@endsection

@section('breadcrumbs')
    {{ Breadcrumbs::render('calendars.index') }}
@endsection

@section('content')
    <h3>Select Calendar</h3>

    <ul>
        @foreach ($calendars as $calendar)
            <li class="py-1 ml-2">
                <a href="{{ route('calendars.calendar-shifts.edit', $calendar) }}" class="text-slate-800 hover:text-orange-700">{{ $calendar->name }}</a>
            </li>
        @endforeach
    </ul>
@endsection
