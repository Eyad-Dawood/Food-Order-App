<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Customer\Address\StoreCustomerAddressRequest;
use App\Http\Requests\Customer\Address\UpdateCustomerAddressRequest;
use Illuminate\Support\Facades\DB;
use App\Exceptions\BusinessException;
use App\Settings\BusinessSettings;

class CustomerAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::guard('api')->user();

        $addresses = $user->addresses()->get();

        return response()->json([
            'data' => $addresses,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerAddressRequest $request , BusinessSettings $settings)
    {
        $validatedData = $request->validated();

        /** @var \App\Models\User $user */
        $user = Auth::guard('api')->user();

        if ($user->addresses()->count() >= $settings->max_addresses_per_user) {
            throw new BusinessException('customer_address.max_addresses_reached', 400);
        }

        $address = DB::transaction(function () use ($validatedData, $user) {
            
            $address = $user->addresses()->create($validatedData);

            if ($address->is_default) {
                $user->addresses()
                     ->where('id', '!=', $address->id)
                     ->update(['is_default' => false]);
            }

            return $address;
        });

        return response()->json([
            'message' => __('customer_address.created_successfully'),
            'data'    => $address,
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerAddressRequest $request, string $id)
    {
        $validatedData = $request->validated();

        /** @var \App\Models\User $user */
        $user = Auth::guard('api')->user();
        
        $address = $user->addresses()->findOrFail($id);

        DB::transaction(function () use ($validatedData, $user, $address) {
            
            $address->update($validatedData);

            if ($address->is_default) {
                $user->addresses()
                     ->where('id', '!=', $address->id)
                     ->update(['is_default' => false]);
            }
        });

        return response()->json([
            'message' => __('customer_address.updated_successfully'),
            'data' => $address,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::guard('api')->user();

        $address = $user->addresses()->findOrFail($id);

        $address->delete();

        return response()->json([
            'message' => __('customer_address.deleted_successfully'),
        ]);
    }
}
