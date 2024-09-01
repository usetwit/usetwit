<?php

namespace App\Http\Controllers;

use App\Exceptions\EndLessThanOrEqualToStartException;
use App\Exceptions\HoursUsedException;
use App\Exceptions\TimeNotSetException;
use App\Http\Requests\Calendars\CalendarShiftsEditRequest;
use App\Http\Requests\Calendars\CalendarShiftsUpdateRequest;
use App\Models\Calendar;
use App\Models\CalendarShift;
use App\Services\CalendarService;
use Illuminate\Support\Arr;

class CalendarShiftsController extends Controller
{
    public function __construct(private readonly CalendarService $calendarService)
    {
    }

    public function edit(Calendar $calendar)
    {
        $calendars = Calendar::all(['id', 'name'])->toArray();

        foreach ($calendars as &$cal) {
            $cal['route'] = route('calendars.calendar-shifts.edit', $cal['id']);
        }

        return view('calendars.calendar-shifts-edit', compact('calendars', 'calendar'));
    }

    public function getCalendarShifts(Calendar $calendar, CalendarShiftsEditRequest $request)
    {
        $shifts = $calendar->calendarShifts()
            ->whereYear('shift_date', $request->input('year'))
            ->get([
                'shift_date',
                'shift1_start',
                'shift1_end',
                'shift2_start',
                'shift2_end',
                'shift3_start',
                'shift3_end',
                'shift4_start',
                'shift4_end',
                'nwd'
            ]);

        $return_array = [];

        foreach ($shifts as $shift) {
            $return_array[$shift->shift_date->format('Y-m-d')] = Arr::except($shift, 'shift_date');
        }

        return $return_array;
    }

    /**
     * @throws TimeNotSetException
     * @throws EndLessThanOrEqualToStartException
     * @throws HoursUsedException
     */
    public function update(Calendar $calendar, CalendarShiftsUpdateRequest $request)
    {
        $dates = $request->input('dates');
        $year = $request->input('year');
        $shifts = [];
        $base = ['calendar_id' => $calendar->id];

        CalendarShift::whereYear('shift_date', $year)
            ->where('calendar_id', $calendar->id)
            ->delete();

        foreach ($dates as $date) {

            $shift_date = ['shift_date' => $date['shift_date']];

            if ($date['nwd'] === true) {
                $shifts[] = $base + $shift_date + [
                        'nwd' => true,
                        'shift1_start' => '00:00',
                        'shift1_end' => '00:00',
                        'shift2_start' => null,
                        'shift2_end' => null,
                        'shift3_start' => null,
                        'shift3_end' => null,
                        'shift4_start' => null,
                        'shift4_end' => null,
                        'total_duration' => 0,
                        'shift1_duration' => 0,
                        'shift2_duration' => 0,
                        'shift3_duration' => 0,
                        'shift4_duration' => 0,
                    ];

            } else if ($date['shift1_end'] !== '00:00') {

                $times = Arr::only($date, [
                    'shift1_start',
                    'shift1_end',
                    'shift2_start',
                    'shift2_end',
                    'shift3_start',
                    'shift3_end',
                    'shift4_start',
                    'shift4_end'
                ]);

                $shifts[] = $base + $times + $shift_date + $this->calendarService->durations($times) + [
                        'nwd' => false,
                    ];
            }
        }

        CalendarShift::insert($shifts);

        return response('Saved Successfully', 200);
    }
}
