<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\UsersIndexGetUsersRequest;
use App\Models\Location;
use App\Models\User;
use App\Services\FilterService;
use App\Settings\GeneralSettings;
use Illuminate\Database\Query\Builder;
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

    public function getLocations(LocationsIndexGetLocationsRequest $request, FilterService $service, GeneralSettings $settings)
    {
        $perPage = $request->input('per_page', $settings->per_page_default);
        $filters = $request->input('filters', []);
        $sort = $request->input('sort', []);
        $visible = $request->input('visible', []);

        $substitutions = ['id' => 'locations.id'];
        $global = [
            'name',
            'address_line_1',
        ];

        $cols = Cache::remember('location_columns', 24 * 60 * 60 * 7, function () {
            $cols = Schema::getColumnListing('locations');

            return $cols;
        });

        $query = DB::table('locations')->select($cols)->leftJoin('addresses', function (Builder $join) {
            $join->on('addresses.addressable_id', 'location.id')->where('addresses.addressable_type', Location::class);
        });

        $service->globalFilter($query, $filters['global']['constraints'][0]['value'], $global, $visible, $substitutions)
            ->filter($query, $filters, ['global'], $substitutions)->sort($query, $sort, ['global'], $substitutions);

        $query = $query->paginate($perPage);
        $total = $query->total();

        $locations = $query->getCollection()->map(function ($location) {

            return array_merge((array) $location, [
                'edit_location_route' => route('admin.locations.edit', $location->slug),
                'created_at' => Carbon::parse($location->created_at)->format('Y-m-d'),
                'updated_at' => Carbon::parse($location->updated_at)->format('Y-m-d'),
                'joined_at' => $location->joined_at === null ? null : Carbon::parse($location->joined_at)->format('Y-m-d'),
            ]);
        });

        return compact('locations', 'total');
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
