<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Locations\GetLocationsRequest;
use App\Models\Location;
use App\Models\User;
use App\Services\FilterService;
use App\Settings\GeneralSettings;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Symfony\Component\Intl\Countries;

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

        return view('admin.locations.index', compact('paginationSettings', 'routeGetLocations', 'dateSettings'));
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
    public function create(GeneralSettings $settings): View
    {
        $dateSettings = $settings->dateSettings();
        $routeCreate = route('admin.locations.create');

        return view('admin.locations.create', compact('dateSettings', 'routeCreate'));
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
    public function edit(Location $location, GeneralSettings $settings)
    {
        $dateSettings = $settings->dateSettings();

        $routes = [
            'delete' => route('admin.locations.destroy', $location),
            'restore' => route('admin.locations.restore', $location),
            'update' => route('admin.locations.update', $location),
        ];

        $countries = collect(Countries::getNames())
            ->map(function (string $name, string $code) {
                return ['code' => $code, 'name' => $name];
            })->values();

        $location->load([
            'address',
            'calendar',
        ]);

        return view('admin.locations.edit', compact('routes', 'countries', 'dateSettings'));
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
