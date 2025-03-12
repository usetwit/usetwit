<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BomOperationsNetwork\UpdateRequest;
use App\Models\BomVersion;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class BomOperationsNetworkController extends Controller
{
    public function edit(BomVersion $bomVersion): View
    {
        $routes = [
            'update' => route('admin.bom-operations-network.update', $bomVersion),
        ];

        $bomVersion->load([
            'bomOperations',
            'bomOperations.calendar.calendarable',
            'bomOperations.operation',
            'bomOperations.successors',
        ]);

        $operations = [];

        foreach ($bomVersion->bomOperations as $op) {
            $operations[] = [
                'id' => $op->id,
                'x' => $op->x,
                'y' => $op->y,
                'type' => $op->type,
                'color' => $op->color,
                'name' => $op->operation->name ?? 'BUFFER',
                'calendar_id' => $op->calendar_id,
                'calendar_name' => $op->calendar->calendarable->name,
                'successors' => $op->successors->pluck('id'),
                'active' => false,
            ];
        }

        return view('admin.bom-operations-network.edit', compact('operations', 'bomVersion', 'routes'));
    }

    public function update(BomVersion $bomVersion, UpdateRequest $request): JsonResponse
    {
        foreach ($request->operations as $operation) {
            $bomOperation = $bomVersion->bomOperations()->findOrFail($operation['id']);

            $bomOperation->update([
                'x' => $operation['x'],
                'y' => $operation['y'],
                'color' => $operation['color'],
            ]);

            $bomOperation->successors()->sync($operation['successors'] ?? []);
        }

        return response()->json('Save successful');
    }
}
