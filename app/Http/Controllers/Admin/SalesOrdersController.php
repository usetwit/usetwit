<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SalesOrders\StoreRequest;
use App\Http\Requests\SalesOrders\StockBomSearchByNameRequest;
use App\Models\Bom;
use App\Models\StockItem;
use App\Settings\GeneralSettings;
use Illuminate\Http\JsonResponse;

class SalesOrdersController extends Controller
{
    public function index()
    {

    }

    public function create(GeneralSettings $settings)
    {
        $dateSettings = $settings->dateSettings();

        return view('sales-orders.sales-orders-create', compact('dateSettings'));
    }

    public function store(StoreRequest $request): JsonResponse
    {
        return response()->json('Sales Order Saved Successfully', 200);
    }

    public function stockBomSearch(StockBomSearchByNameRequest $request)
    {
        $name = $request->input('name');

        $bomItemsResults = Bom::select(['name', 'description'])->where('name', 'like', $name . '%')->limit(3)
                              ->orderBy('name')->get();

        $stockItemsResults = StockItem::select(['name', 'description'])->where('name', 'like', $name . '%')
                                      ->limit(3)->orderBy('name')->get();

        $array = [];
        if (count($bomItemsResults)) {
            $array[] = [
                'label' => 'BOM Items',
                'items' => $bomItemsResults,
            ];
        }

        if (count($stockItemsResults)) {
            $array[] = [
                'label' => 'Stock Items',
                'items' => $stockItemsResults,
            ];
        }

        return $array;
    }
}
