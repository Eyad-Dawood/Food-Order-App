<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Http\Requests\Restaurant\StoreRestaurantRequest;
use App\Http\Requests\Restaurant\UpdateRestaurantRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Exceptions\BusinessException;
use App\Settings\BusinessSettings;
use App\Http\Resources\RestaurantResource;

class RestaurantController extends Controller
{
    public function index()
    {
        $restaurants = Restaurant::where('is_active', true)->get();

        return response()->json([
            'data' => RestaurantResource::collection($restaurants),
        ]);
    }

    public function store(StoreRestaurantRequest $request , BusinessSettings $settings)
    {
        $validatedData = $request->validated();
        
        $validatedData['is_active'] = false; // New restaurants are inactive by default, pending admin approval

        /** @var \App\Models\User $user */
        $user = Auth::guard('api')->user();

        if ($user->restaurants()->count() >= $settings->max_restaurants_per_user) {
            throw new BusinessException('restaurant.max_restaurants_reached', 400);
        }
        
        $validatedData['manager_id'] = $user->id;

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('restaurants/logos', 'public');
            $validatedData['logo_path'] = $path;
        }

        $restaurant = Restaurant::create($validatedData);

        return response()->json([
            'message' => __('restaurant.created_successfully'),
            'data'    => new RestaurantResource($restaurant),
        ], 201);
    }

    public function show(string $id)
    {
        $restaurant = Restaurant::findOrFail($id);

        return response()->json([
            'data' => $restaurant,
        ]);
    }

    public function update(UpdateRestaurantRequest $request, string $id)
    {
        $validatedData = $request->validated();

        /** @var \App\Models\User $user */
        $user = Auth::guard('api')->user();

        $restaurant = $user->restaurants()->findOrFail($id);

        if ($request->hasFile('logo')) {
            //delete old logo if exists
            if ($restaurant->logo_path) {
                Storage::disk('public')->delete($restaurant->logo_path);
            }
            //add new logo
            $path = $request->file('logo')->store('restaurants/logos', 'public');
            $validatedData['logo_path'] = $path;
        }

        $restaurant->update($validatedData);

        return response()->json([
            'message' => __('restaurant.updated_successfully'),
            'data'    => new RestaurantResource($restaurant),
        ]);
    }

    public function destroy(string $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::guard('api')->user();

        $restaurant = $user->restaurants()->findOrFail($id);
        
        //delete the restaurant's logo if it exists
        if ($restaurant->logo_path) {
            Storage::disk('public')->delete($restaurant->logo_path);
        }

        $restaurant->delete();

        return response()->json([
            'message' => __('restaurant.deleted_successfully'),
        ]);
    }

    //admin only
    public function deActivate(string $id)
    {
        $restaurant = Restaurant::findOrFail($id);
        $restaurant->is_active = false;
        $restaurant->save();

        return response()->json([
            'message' => __('restaurant.updated_successfully'),
            'data'    => new RestaurantResource($restaurant),
        ]);
    }
    //admin only
    public function activate(string $id)
    {
        $restaurant = Restaurant::findOrFail($id);
        $restaurant->is_active = true;
        $restaurant->save();

        return response()->json([
            'message' => __('restaurant.updated_successfully'),
            'data'    => new RestaurantResource($restaurant),
        ]);
    }

    public function myRestaurants()
    {
        /** @var \App\Models\User $user */
        $user = Auth::guard('api')->user();

        $restaurants = $user->restaurants()->get();

        return response()->json([
            'data' => RestaurantResource::collection($restaurants),
        ]);
    }
}