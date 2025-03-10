<?php

namespace App\Services;

use Exception;
use Carbon\Carbon;


/**
 * Backward schedules a given duration (in minutes) from an initial date/time
 * based on your shifts. If a day is not defined in $dates, it is treated as a full
 * 24‑hour working day. If a day is flagged as non‑working (nwd == 1), it will be skipped.
 *
 * @param mixed $initialDateTime A Carbon instance or a date/time string, e.g. "2025-12-12 10:30".
 * @param int $totalMinutes Total minutes to schedule backward.
 * @param array $dates Associative array of dates with shift info.
 * Example:
 * [
 * '2025-12-12' => [
 * 'nwd' => 0,
 * 'shift1_start' => '08:00',
 * 'shift1_end' => '12:00',
 * 'shift2_start' => '13:00',
 * 'shift2_end' => '17:00',
 * ],
 * // other days...
 * ]
 *
 * @return Carbon The resulting scheduled time as a Carbon instance.
 */
class SchedulingService
{
    public function scheduleOperation(Carbon $initialDateTime, int $totalMinutes, array $dates): Carbon
    {
        $currentDateTime = $initialDateTime->copy();

        while ($totalMinutes > 0) {
            // Get the current date (YYYY-MM-DD)
            $currentDate = $currentDateTime->format('Y-m-d');

            // If the day is not specified, treat it as a full 24-hour working day.
            if (!isset($dates[$currentDate])) {
                $dayData = [
                    'shift1_start' => '00:00',
                    'shift1_end' => '00:00',
                ];
            } else {
                $dayData = $dates[$currentDate];

                // If the day is flagged as non‑working, skip to the previous day.
                if (isset($dayData['nwd']) && $dayData['nwd'] == 1) {
                    $currentDateTime->subDay()->endOfDay();
                    continue;
                }
            }

            // Build the shifts array (supporting up to 4 shifts)
            $shifts = [];
            for ($i = 1; $i <= 4; $i++) {
                if (isset($dayData["shift{$i}_start"], $dayData["shift{$i}_end"])) {
                    $shiftStart = Carbon::parse("{$currentDate} " . $dayData["shift{$i}_start"]);
                    $shiftEnd = Carbon::parse("{$currentDate} " . $dayData["shift{$i}_end"]);
                    // If the shift end time is "00:00", treat it as the start of the next day.
                    if ($dayData["shift{$i}_end"] === '00:00') {
                        $shiftEnd->addDay();
                    }
                    $shifts[] = [
                        'start' => $shiftStart,
                        'end' => $shiftEnd,
                    ];
                }
            }

            $allocated = false;
            // Process shifts in reverse order (from the last shift backwards)
            foreach (array_reverse($shifts) as $shift) {
                // Clamp the current time to the shift's end if it's later.
                if ($currentDateTime->gt($shift['end'])) {
                    $currentDateTime = $shift['end']->copy();
                }
                // If current time is within the shift...
                if ($currentDateTime->between($shift['start'], $shift['end'])) {
                    // Determine the available minutes from the shift start up to current time.
                    $availableMinutes = $currentDateTime->diffInMinutes($shift['start']);
                    if ($availableMinutes > 0) {
                        $minutesToSubtract = min($availableMinutes, $totalMinutes);
                        $currentDateTime->subMinutes($minutesToSubtract);
                        $totalMinutes -= $minutesToSubtract;
                        $allocated = true;
                        if ($totalMinutes <= 0) {
                            break;
                        }
                    }
                }
            }
            // If no minutes were allocated on this day, move to the previous day.
            if ($totalMinutes > 0 && !$allocated) {
                $currentDateTime->subDay()->endOfDay();
            }
        }

        return $currentDateTime;
    }

// -------------------------
// Step 3: Define the Backward Scheduling Function
// -------------------------
// This recursive function uses the successors mapping. For a given operation,
// it recursively schedules each successor (the ones to its right) so that it can
// determine the earliest start time among them. Then, it schedules the current
// operation to finish exactly by that earliest successor start.

    /**
     * @throws Exception
     */
    public function scheduleBackward(string $operation, array $successors, array &$scheduledTimes, $depth = 0)
    {
        if ($depth > 1000) {
            throw new Exception('Exceeded maximum scheduling depth! Possible infinite loop.');
        }

        if (isset($scheduledTimes[$operation])) {
            return $scheduledTimes[$operation];
        }

        $nextOps = $successors[$operation] ?? [];
        if (empty($nextOps)) {
            $scheduledTimes[$operation] = $this->scheduleOperation($operation, null);
            return $scheduledTimes[$operation];
        }

        $successorStartTimes = [];
        foreach ($nextOps as $nextOp) {
            $schedForSuccessor = $this->scheduleBackward($nextOp, $successors, $scheduledTimes, $depth + 1);
            $successorStartTimes[] = $schedForSuccessor['start'];
        }

        $earliestSuccessorStart = min($successorStartTimes);
        $scheduledTimes[$operation] = $this->scheduleOperation($operation, $earliestSuccessorStart);

        return $scheduledTimes[$operation];
    }
}





// -------------------------
// Step 4: Schedule All Operations
// -------------------------
// To ensure every operation in our network gets scheduled, we iterate over
// all operations (the keys of our successors mapping) and call the recursive function.
//        $scheduledTimes = [];
//        $allOperations = array_keys($successors);
//        foreach ($allOperations as $op) {
//            scheduleBackward($op, $successors, $scheduledTimes);
//        }
//
//// -------------------------
//// Step 5: Output the Schedule
//// -------------------------
//        foreach ($scheduledTimes as $operation => $times) {
//            echo "$operation: starts at {$times['start']}, finishes at {$times['finish']}\n"
//}
//    }
