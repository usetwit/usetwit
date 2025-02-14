<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Locations\GetLocationsRequest;
use App\Models\Location;
use App\Services\FilterService;
use App\Settings\GeneralSettings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LocationsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(GeneralSettings $settings)
    {
        $dateSettings = $settings->dateSettings();
        $paginationSettings = $settings->paginationSettings();

        $routeGetLocations = route('admin.locations.get-locations');

        return view('locations.locations-index', compact('paginationSettings', 'routeGetLocations', 'dateSettings'));
    }

    public function getLocations(GetLocationsRequest $request, FilterService $service, GeneralSettings $settings): JsonResponse
    {
        $perPage = $request->input('per_page', $settings->per_page_default);
        $filters = $request->input('filters', []);
        $sorts = $request->input('sort', []);
        $visible = $request->input('visible', []);
        $substitutions = ['id' => 'locations.id'];
        $global = [
            'id',
            'name',
            'address_line_1',
            'address_line_2',
            'address_line_3',
            'postcode',
            'country_code',
            'country_name',
        ];

        $location_cols = Cache::rememberForever('location_columns', function () {
            $cols = Schema::getColumnListing('locations');
            $cols = array_diff($cols, ['description']);

            return array_map(fn ($value) => 'locations.'.$value, $cols);
        });

        $address_cols = Cache::rememberForever('address_columns', function () {
            $cols = Schema::getColumnListing('addresses');
            $cols = array_diff($cols, ['id']);

            return array_map(fn ($value) => 'addresses.'.$value, $cols);
        });

        $cols = array_merge($location_cols, $address_cols);

        $query = DB::table('locations')
            ->select($cols)
            ->leftJoin('addresses', function ($join) {
                $join->on('addresses.addressable_id', 'locations.id')->where('addresses.addressable_type', Location::class);
            });

        $service->filterAndSort($query, $filters, $global, $visible, ['global'], $substitutions, $sorts);

        $query = $query->paginate($perPage);
        $total = $query->total();

        $locations = $query->getCollection()->map(function ($location) {
            return array_merge((array) $location, [
                'edit_location_route' => route('admin.locations.edit', $location->slug),
                'created_at' => Carbon::parse($location->created_at)->format('Y-m-d'),
                'updated_at' => Carbon::parse($location->updated_at)->format('Y-m-d'),
            ]);
        });

        return response()->json(compact('locations', 'total'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
