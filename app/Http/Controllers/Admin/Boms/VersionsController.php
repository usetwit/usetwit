<?php

namespace App\Http\Controllers\Admin\Boms;

use App\Http\Controllers\Controller;
use App\Models\BomVersion;
use Illuminate\Http\Request;

class VersionsController extends Controller
{
    public function edit(BomVersion $bomVersion)
    {
        $routes = [
            'update' => route('admin.boms.versions.update', $bomVersion),
        ];

        return view('admin.boms.versions.edit', compact('bomVersion', 'routes'));
    }
}
