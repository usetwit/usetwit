<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Boms\CheckNameRequest;
use App\Http\Requests\Admin\Boms\GetBomsRequest;
use App\Http\Requests\Admin\Boms\UpdateRequest;
use App\Models\Bom;
use App\Services\FilterService;
use App\Settings\GeneralSettings;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
                        ->map(fn($version) => "v{$version}");

        return view('admin.boms.edit', compact('bom', 'routes', 'versions'));
    }


    public function update(Bom $bom, UpdateRequest $request): JsonResponse
    {
        $bom->update($request->validated());

        return response()->json([
            'message' => 'BOM Updated Successfully',
        ]);
    }

    public function checkName(CheckNameRequest $request): JsonResponse
    {
        $exists = Bom::where('name', $request->input('name'))->exists();

        return response()->json(['exists' => $exists]);
    }

    public function index(GeneralSettings $settings)
    {
        $dateSettings = $settings->dateSettings();
        $paginationSettings = $settings->paginationSettings();

        $routes = [
            'get_boms' => route('admin.boms.get-boms'),
        ];

        return view('admin.boms.index', compact('dateSettings', 'paginationSettings', 'routes'));
    }

    public function getBoms(GetBomsRequest $request, FilterService $service, GeneralSettings $settings): JsonResponse
    {
        $perPage = $request->input('per_page', $settings->per_page_default);
        $filters = $request->input('filters', []);
        $sorts = $request->input('sort', []);
        $visible = $request->input('visible', []);

        $substitutions = ['id' => 'boms.id'];
        $global = [
            'id',
            'name',
        ];

        $query = DB::table('boms');

        $service->filterAndSort($query, $filters, $global, $visible, ['global'], $substitutions, $sorts);

        $query = $query->paginate($perPage);
        $total = $query->total();

        $boms = $query->getCollection()->map(function ($bom) {
            return array_merge((array)$bom, [
                'edit_bom_route' => route('admin.boms.edit', $bom->slug),
                'created_at' => Carbon::parse($bom->created_at)->format('Y-m-d'),
                'updated_at' => Carbon::parse($bom->updated_at)->format('Y-m-d'),
            ]);
        });

        return response()->json(compact('boms', 'total'));
    }
}
