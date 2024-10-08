<?php

namespace Tests\Feature;

use App\Models\Calendar;
use App\Models\CalendarShift;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalendarShiftsFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    public function test_calendar_shift_edit_can_be_seen_if_user_has_permission()
    {
        $this->setUserWithPermissions('calendars.update');

        $calendar = Calendar::factory()
                            ->create();

        $response = $this->get(route('calendars.calendar-shifts.edit', $calendar));

        $response->assertStatus(200)
                 ->assertSee($calendar->name);
    }

    public function test_calendar_shift_edit_inaccessible_to_user_without_permission()
    {
        $this->setUserWithPermissions();

        $calendar = Calendar::factory()
                            ->create();

        $response = $this->get(route('calendars.calendar-shifts.edit', $calendar));

        $response->assertStatus(403);
    }

    public function test_user_cannot_see_calendar_shifts_if_not_authenticated()
    {
        $calendar = Calendar::factory()
                            ->create();

        $response = $this->get(route('calendars.calendar-shifts.edit', $calendar));

        $response->assertStatus(302);
    }

    public function test_calendar_shifts_can_be_retrieved_via_post_request_if_user_has_permission()
    {
        $this->setUserWithPermissions('calendars.update');

        $calendarShift = CalendarShift::factory()
                                      ->create();

        $postData = [
            'year' => $calendarShift->shift_date->year,
        ];

        $response = $this->post(route('calendars.calendar-shifts.get-calendar-shifts', $calendarShift->calendar_id),
            $postData);

        $response->assertStatus(200);

        $responseData = $response->json();

        $shiftDate = $calendarShift->shift_date->format('Y-m-d');
        $expectedData = [
            'shift1_start' => $calendarShift->shift1_start,
            'shift1_end' => $calendarShift->shift1_end,
            'shift2_start' => $calendarShift->shift2_start,
            'shift2_end' => $calendarShift->shift2_end,
            'shift3_start' => $calendarShift->shift3_start,
            'shift3_end' => $calendarShift->shift3_end,
            'shift4_start' => $calendarShift->shift4_start,
            'shift4_end' => $calendarShift->shift4_end,
            'nwd' => $calendarShift->nwd,
        ];

        $this->assertArrayHasKey($shiftDate, $responseData);
        $this->assertEquals($expectedData, $responseData[$shiftDate]);
    }

    public function test_calendar_shift_can_be_updated_via_patch_request_with_nwd_as_false()
    {
        $this->setUserWithPermissions('calendars.update');

        $calendar = Calendar::factory()
                            ->create();
        $calendarShift = CalendarShift::factory()
                                      ->make(['calendar_id' => $calendar->id,]);

        $dates = array_merge($calendarShift->attributesToArray(),
            ['shift_date' => $calendarShift->shift_date->format('Y-m-d'),]);

        $updateData = [
            'year' => $calendarShift->shift_date->year,
            'dates' => [$dates],
        ];

        $response = $this->patch(route('calendars.calendar-shifts.update', $calendar), $updateData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('calendar_shifts', $dates,);
    }

    public function test_calendar_shift_can_be_updated_via_patch_request_when_nwd_is_true_even_if_other_shifts_specified()
    {
        $this->setUserWithPermissions('calendars.update');

        $calendar = Calendar::factory()
                            ->create();
        $calendarShift = CalendarShift::factory()
                                      ->create([
                                          'calendar_id' => $calendar->id,
                                          'nwd' => true,
                                      ]);

        $dates = array_merge($calendarShift->attributesToArray(), [
            'shift_date' => $calendarShift->shift_date->format('Y-m-d'),
            'shift1_start' => '00:00',
            'shift1_end' => '01:00',
            'shift2_start' => '02:00',
            'shift2_end' => '03:00',
            'shift3_start' => null,
            'shift3_end' => null,
            'shift4_start' => null,
            'shift4_end' => null,
        ]);

        $updateData = [
            'year' => $calendarShift->shift_date->year,
            'dates' => [$dates],
        ];

        $response = $this->patch(route('calendars.calendar-shifts.update', $calendar), $updateData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('calendar_shifts', [
            'calendar_id' => $calendar->id,
            'nwd' => true,
            'shift_date' => $calendarShift->shift_date->format('Y-m-d'),
            'shift1_start' => '00:00:00',
            'shift1_end' => '00:00:00',
            'shift2_start' => null,
            'shift2_end' => null,
            'shift3_start' => null,
            'shift3_end' => null,
            'shift4_start' => null,
            'shift4_end' => null,
        ]);
    }

    public function test_calendar_shifts_table_should_be_empty_if_nwd_is_false_and_shift1_start_and_end_are_midnight()
    {
        $this->setUserWithPermissions('calendars.update');

        $calendarShift = CalendarShift::factory()
                                      ->create();

        $dates = array_merge($calendarShift->attributesToArray(), [
            'shift_date' => $calendarShift->shift_date->format('Y-m-d'),
            'shift1_start' => '00:00',
            'shift1_end' => '00:00',
            'shift2_start' => null,
            'shift2_end' => null,
            'shift3_start' => null,
            'shift3_end' => null,
            'shift4_start' => null,
            'shift4_end' => null,
        ]);

        $updateData = [
            'year' => $calendarShift->shift_date->year,
            'dates' => [$dates],
        ];

        $response = $this->patch(route('calendars.calendar-shifts.update', $calendarShift->calendar_id), $updateData);

        $response->assertStatus(200);

        $this->assertDatabaseCount('calendar_shifts', 0);
    }
}
