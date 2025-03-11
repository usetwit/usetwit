<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BomOperationsNetwork\UpdateRequest;
use App\Models\Bom;
use App\Models\BomOperation;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class BomOperationsNetworkController extends Controller
{
    public function edit(Bom $bom): View
    {
        $routes = [
            'update' => route('admin.bom-operations-network.update', $bom),
        ];

        $bom->load([
            'bomOperations',
            'bomOperations.calendar.calendarable',
            'bomOperations.operation',
            'bomOperations.successors',
        ]);

        $operations = [];

        foreach ($bom->bomOperations as $op) {
            $operations[] = [
                'id' => $op->id,
                'x' => $op->x,
                'y' => $op->y,
                'type' => $op->type,
                'color' => $op->color,
                'default_color' => $op->operation?->color,
                'name' => $op->operation->name ?? 'BUFFER',
                'calendar_id' => $op->calendar_id,
                'calendar_name' => $op->calendar->calendarable->name,
                'successors' => $op->successors->pluck('id') ?? [],
                'active' => false,
            ];
        }

        return view('admin.bom-operations-network.edit', compact('operations', 'bom', 'routes'));
    }

    public function update(Bom $bom, UpdateRequest $request): JsonResponse
    {
        foreach ($request->operations as $operation) {
            BomOperation::where('id', $operation['id'])->update([
                'x' => $operation['x'],
                'y' => $operation['y'],
                'color' => $operation['color'],
            ]);
        }

        return response()->json('success');
    }
}
