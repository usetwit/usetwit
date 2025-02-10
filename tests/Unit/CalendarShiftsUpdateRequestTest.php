<?php

namespace Tests\Unit;

use App\Http\Requests\Calendars\CalendarShiftsUpdateRequest;
use Faker\Generator;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;
use Faker\Factory as FakerFactory;

class CalendarShiftsUpdateRequestTest extends TestCase
{
    use WithFaker;

    protected function sampleData(array $overrides = []): array
    {
        $defaultData = [
            'year' => $this->faker->numberBetween(2020, 2050),
            'dates' => [
                [
                    'shift_date' => '2024-08-18',
                    'nwd' => false,
                    'shift1_start' => '00:00',
                    'shift1_end' => '01:00',
                    'shift2_start' => '02:00',
                    'shift2_end' => '03:00',
                    'shift3_start' => '04:00',
                    'shift3_end' => '05:00',
                    'shift4_start' => '06:00',
                    'shift4_end' => '00:00',
                ]
            ]
        ];

        return array_replace_recursive($defaultData, $overrides);
    }

    protected function validate(array $data)
    {
        $request = new CalendarShiftsUpdateRequest();
        return Validator::make($data, $request->rules());
    }

    public function test_dates_array_requires_at_least_one_entry()
    {
        $validator = $this->validate(['dates' => []]);
        $this->assertTrue($validator->errors()->has('dates'));

        $validator = $this->validate($this->sampleData([
            'dates' => [
                [
                    'shift_date' => '2024-08-18',
                    'nwd' => false,
                    'shift1_start' => '00:00',
                    'shift1_end' => '01:00',
                ]
            ]
        ]));
        $this->assertFalse($validator->errors()->has('dates'));
    }

    public function test_shift_date_format()
    {
        $validator = $this->validate($this->sampleData(['dates' => [['shift_date' => '08-18-2024']]]));
        $this->assertTrue($validator->errors()->has('dates.0.shift_date'));

        $validator = $this->validate($this->sampleData(['dates' => [['shift_date' => '2024-08-18']]]));
        $this->assertFalse($validator->errors()->has('dates.0.shift_date'));
    }

    public function test_nwd_boolean()
    {
        $validator = $this->validate($this->sampleData(['dates' => [['nwd' => 'yes']]]));
        $this->assertTrue($validator->errors()->has('dates.0.nwd'));

        $validator = $this->validate($this->sampleData(['dates' => [['nwd' => true]]]));
        $this->assertFalse($validator->errors()->has('dates.0.nwd'));

        $validator = $this->validate($this->sampleData(['dates' => [['nwd' => false]]]));
        $this->assertFalse($validator->errors()->has('dates.0.nwd'));
    }

    public function test_year_is_required()
    {
        $validator = $this->validate($this->sampleData(['year' => null]));
        $this->assertTrue($validator->errors()->has('year'));
    }

    public function test_year_must_be_an_integer()
    {
        $validator = $this->validate($this->sampleData(['year' => 'invalid_year']));
        $this->assertTrue($validator->errors()->has('year'));

        $validator = $this->validate($this->sampleData(['year' => 2024.5]));
        $this->assertTrue($validator->errors()->has('year'));
    }

    public function test_year_must_be_between_2020_and_2050()
    {
        $validator = $this->validate($this->sampleData(['year' => 2019]));
        $this->assertTrue($validator->errors()->has('year'));

        $validator = $this->validate($this->sampleData(['year' => 2051]));
        $this->assertTrue($validator->errors()->has('year'));

        $validator = $this->validate($this->sampleData(['year' => 2024]));
        $this->assertFalse($validator->errors()->has('year'));
    }

    public function test_year_must_be_a_valid_numeric_year()
    {
        $validator = $this->validate($this->sampleData(['year' => 'abcd']));
        $this->assertTrue($validator->errors()->has('year'));

        $validator = $this->validate($this->sampleData(['year' => '@2024']));
        $this->assertTrue($validator->errors()->has('year'));
    }

    public function test_partial_shift_entries()
    {
        $validator = $this->validate($this->sampleData([
            'dates' => [
                [
                    'shift1_start' => '00:00',
                    'shift1_end' => null,
                ]
            ]
        ]));
        $this->assertTrue($validator->errors()->has('dates.0.shift1_end'));

        $validator = $this->validate($this->sampleData([
            'dates' => [
                [
                    'shift1_start' => '00:00',
                    'shift1_end' => '01:00',
                    'shift2_start' => '02:00',
                    'shift2_end' => null,
                ]
            ]
        ]));
        $this->assertTrue($validator->errors()->has('dates.0.shift2_end'));

        $validator = $this->validate($this->sampleData([
            'dates' => [
                [
                    'shift1_start' => '00:00',
                    'shift1_end' => '01:00',
                    'shift2_start' => '02:00',
                    'shift2_end' => '03:00',
                    'shift3_start' => '04:00',
                    'shift3_end' => null,
                ]
            ]
        ]));
        $this->assertTrue($validator->errors()->has('dates.0.shift3_end'));

        $validator = $this->validate($this->sampleData([
            'dates' => [
                [
                    'shift1_start' => '00:00',
                    'shift1_end' => '01:00',
                    'shift2_start' => '02:00',
                    'shift2_end' => '03:00',
                    'shift3_start' => '04:00',
                    'shift3_end' => '05:00',
                    'shift4_start' => '06:00',
                    'shift4_end' => null,
                ]
            ]
        ]));
        $this->assertTrue($validator->errors()->has('dates.0.shift4_end'));

        $validator = $this->validate($this->sampleData([
            'dates' => [
                [
                    'shift1_start' => null,
                    'shift1_end' => '01:00',
                ]
            ]
        ]));
        $this->assertTrue($validator->errors()->has('dates.0.shift1_start'));

        $validator = $this->validate($this->sampleData([
            'dates' => [
                [
                    'shift1_start' => '00:00',
                    'shift1_end' => '01:00',
                    'shift2_start' => null,
                    'shift2_end' => '02:00',
                ]
            ]
        ]));
        $this->assertTrue($validator->errors()->has('dates.0.shift2_start'));

        $validator = $this->validate($this->sampleData([
            'dates' => [
                [
                    'shift1_start' => '00:00',
                    'shift1_end' => '01:00',
                    'shift2_start' => '02:00',
                    'shift2_end' => '03:00',
                    'shift3_start' => null,
                    'shift3_end' => '04:00',
                ]
            ]
        ]));
        $this->assertTrue($validator->errors()->has('dates.0.shift3_start'));

        $validator = $this->validate($this->sampleData([
            'dates' => [
                [
                    'shift1_start' => '00:00',
                    'shift1_end' => '01:00',
                    'shift2_start' => '02:00',
                    'shift2_end' => '03:00',
                    'shift3_start' => '04:00',
                    'shift3_end' => '05:00',
                    'shift4_start' => null,
                    'shift4_end' => '06:00',
                ]
            ]
        ]));
        $this->assertTrue($validator->errors()->has('dates.0.shift4_start'));
    }

    public function test_shift1_end_greater_than_shift1_start_or_midnight()
    {
        $validator = $this->validate($this->sampleData([
            'dates' => [['shift1_start' => '02:00', 'shift1_end' => '03:00']]
        ]));
        $this->assertFalse($validator->errors()->has('dates.0.shift1_end'));

        $validator = $this->validate($this->sampleData([
            'dates' => [['shift1_start' => '02:00', 'shift1_end' => '00:00']]
        ]));
        $this->assertFalse($validator->errors()->has('dates.0.shift1_end'));
    }

    public function test_shift2_end_greater_than_shift2_start_or_midnight()
    {
        $validator = $this->validate($this->sampleData([
            'dates' => [['shift2_start' => '03:00', 'shift2_end' => '04:00']]
        ]));
        $this->assertFalse($validator->errors()->has('dates.0.shift2_end'));

        $validator = $this->validate($this->sampleData([
            'dates' => [['shift2_start' => '03:00', 'shift2_end' => '00:00']]
        ]));
        $this->assertFalse($validator->errors()->has('dates.0.shift2_end'));
    }

    public function test_shift3_end_greater_than_shift3_start_or_midnight()
    {
        $validator = $this->validate($this->sampleData([
            'dates' => [['shift3_start' => '04:00', 'shift3_end' => '05:00']]
        ]));
        $this->assertFalse($validator->errors()->has('dates.0.shift3_end'));

        $validator = $this->validate($this->sampleData([
            'dates' => [['shift3_start' => '04:00', 'shift3_end' => '00:00']]
        ]));
        $this->assertFalse($validator->errors()->has('dates.0.shift3_end'));
    }

    public function test_shift4_end_greater_than_shift4_start_or_midnight()
    {
        $validator = $this->validate($this->sampleData([
            'dates' => [['shift4_start' => '06:00', 'shift4_end' => '00:00']]
        ]));
        $this->assertFalse($validator->errors()->has('dates.0.shift4_end'));

        $validator = $this->validate($this->sampleData([
            'dates' => [['shift4_start' => '06:00', 'shift4_end' => '07:00']]
        ]));
        $this->assertFalse($validator->errors()->has('dates.0.shift4_end'));
    }
}
