<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use Illuminate\Http\Request;

class CalendarsController extends Controller
{
    public function index()
    {
        $calendars = Calendar::orderBy('name')->get();

        return view('calendars.calendars-index', compact('calendars'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Calendar $calendar)
    {
        $calendars = Calendar::all();

        return view('calendars.calendar-shifts-edit', compact('calendar', 'calendars'));
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
