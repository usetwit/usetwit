<?php

namespace App\Http\Controllers;

use App\Http\Requests\SalesOrders\SalesOrdersStoreRequest;
use App\Http\Requests\SalesOrders\StockBomSearchByNameRequest;
use App\Models\Bom;
use App\Models\StockItem;
use App\Settings\GeneralSettings;

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

    public function store(SalesOrdersStoreRequest $request)
    {
        return response('Sales Order Saved Successfully', 200);
    }

    public function stockBomSearch(StockBomSearchByNameRequest $request)
    {
        $long_id = $request->input('long_id');

        $bomItemsResults = Bom::select(['long_id', 'description'])->where('long_id', 'like', $long_id . '%')->limit(3)
                              ->orderBy('long_id')->get();

        $stockItemsResults = StockItem::select(['long_id', 'description'])->where('long_id', 'like', $long_id . '%')
                                      ->limit(3)->orderBy('long_id')->get();

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
