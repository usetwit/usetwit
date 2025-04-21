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
            'update' => route('admin.boms.update', $bom),
            'check_name' => route('admin.boms.checkName'),
        ];

        $versions = $bom->bomVersions()
                        ->orderByDesc('version')
                        ->pluck('version', 'id')
                        ->map(fn ($version) => "v{$version}");

        return view('admin.bom.edit', compact('bom', 'routes', 'versions'));
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
