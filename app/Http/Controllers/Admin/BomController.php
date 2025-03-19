<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Boms\CheckNameRequest;
use App\Http\Requests\Admin\Boms\UpdateRequest;
use App\Http\Requests\Users\CheckUsernameRequest;
use App\Models\Bom;
use App\Models\BomVersion;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BomController extends Controller
{
    public function edit(Bom $bom)
    {
        $routes = [
            'update' => route('admin.bom.update', $bom),
            'check_name' => route('admin.bom.checkName'),
        ];

        return view('admin.bom.edit', compact('bom', 'routes'));
    }

    public function update(Bom $bom, UpdateRequest $request): JsonResponse
    {
        $bom->update($request->validated());

        return response()->json([
            'message' => 'BOM Updated Successfully',
        ]);
    }

    public function checkName(CheckNameRequest $request):JsonResponse
    {
        $exists = Bom::where('name', $request->input('name'))->exists();

        return response()->json(['exists' => $exists]);
    }
}
