<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CalendarsController extends Controller
{
    public function index()
    {
        $calendars = DB::table('calendars')
            ->leftJoin('locations', function ($join) {
                $join->on('calendars.calendarable_id', '=', 'locations.id')
                    ->where('calendars.calendarable_type', 'App\\Models\\Location');
            })
            ->leftJoin('shifts', function ($join) {
                $join->on('calendars.calendarable_id', '=', 'shifts.id')
                    ->where('calendars.calendarable_type', 'App\\Models\\Shift');
            })
            ->select('calendars.*', DB::raw('COALESCE(locations.name, shifts.name) AS name'))
            ->orderBy('name')
            ->get();

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

    public function show()
    {
        //
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
