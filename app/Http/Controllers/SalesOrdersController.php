<?php

namespace App\Http\Controllers;

use App\Http\Requests\SalesOrders\SalesOrdersStoreRequest;
use App\Http\Requests\SalesOrders\StockBomSearchByNameRequest;
use App\Models\Bom;
use App\Models\StockItem;
use App\Settings\GeneralSettings;

class SalesOrdersController extends Controller
{
    public function index(){

    }

    public function create(GeneralSettings $settings)
    {
        $date_format = $settings->date_format_input;

        return view('sales-orders.sales-order-create', compact('date_format'));
    }

    public function store(SalesOrdersStoreRequest $request)
    {
        return response('Sales Order Saved Successfully', 200);
    }

    public function stockBomSearch(StockBomSearchByNameRequest $request)
    {
        $name = $request->input('name');

        $bomItemsResults = Bom::select(['name', 'description'])
            ->where('name', 'like', $name . '%')
            ->limit(3)
            ->orderBy('name')
            ->get();

        foreach ($bomItemsResults as $key => $value) {
            $bomItemsResults[$key]['value'] = $value->name . 'value';
        }

        $stockItemsResults = StockItem::select(['name', 'description'])
            ->where('name', 'like', $name . '%')
            ->whereNull('bom_id')
            ->limit(3)
            ->orderBy('name')
            ->get();

        foreach ($stockItemsResults as $key => $value) {
            $stockItemsResults[$key]['value'] = $value->name . 'value';
        }

        $array = [];
        if(count($bomItemsResults)) {
            $array[] = [
                'label' => 'BOM Items',
                'items' => $bomItemsResults
            ];
        }

        if(count($stockItemsResults)) {
            $array[] = [
                'label' => 'Stock Items',
                'items' => $stockItemsResults
            ];
        }

        return $array;
    }
}
