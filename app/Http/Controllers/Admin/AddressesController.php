<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Addresses\CreateUserRequest;
use App\Models\Address;
use App\Models\Company;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;

class AddressesController extends Controller
{
    public function userCreate(User $user, CreateUserRequest $request)
    {
        $user->addresses()->create($request->validated());

        $user->load('addresses');

        return response()->json([
            'addresses' => $user->addresses,
            'message' => 'Address added',
        ], 201);
    }

    private function makeDefault(User|Company|Location $model, Address $address)
    {
        $address->update(['is_default' => true]);

        return response()->json([
            'addresses' => $model->addresses,
            'message' => 'Address set as default',
        ]);
    }

    public function userMakeDefault(User $user, Address $address)
    {
        return $this->makeDefault($user, $address);
    }

    public function userDestroy(User $user, Address $address)
    {
        $address->delete();

        return response()->json([
            'addresses' => $user->addresses,
            'message' => 'Address deleted',
        ]);
    }
}
