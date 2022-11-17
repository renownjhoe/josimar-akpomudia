<?php

namespace App\Http\Controllers;

use App\Models\Drone;
use App\Models\Loader;
use App\Models\Products;
use App\Models\Timeline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class LoaderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            'status' => true,
            'message' => "Successful",
            'data' => Loader::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            $user = Auth()->user();
            $validate = Validator::make($request->all(), [
                'customer' => 'required',
                'drone_id' => 'required',
                'product_id' => 'required',
            ]);

            if($validate->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validate->errors()
                ], 401);
            }

            $drone = Drone::find($request->drone_id);
            $product = Products::find($request->product_id);

            if($drone->battery_level <= 25){
                return response()->json([
                    'status' => false,
                    'message' => "This drone cannot go for delivery. Charge it."
                ]);
            }

            if($drone->weight_limit < $product->weight){
                return response()->json([
                    'status' => false,
                    'message' => "The product weight-{$product->weight} is more that what the drone-{$drone->weight_limit} can take. Check out another drone."
                ]);
            }

            $load_checker = Loader::where([['vendor', $user->id], ['customer', $request->customer], ['drone_id', $request->drone_id], ['product_id', $request->product_id], ['status', '!=', 5]])->get();
            if(count($load_checker) > 1){
                return response()->json([
                    'status' => false,
                    'message' => 'You cannot perform same task when its still in progress'
                ]);
            }

            $load = Loader::create([
                'vendor' => $user->id,
                'customer' => $request->customer,
                'drone_id' => $request->drone_id,
                'product_id' => $request->product_id,
                'status' => 2,
            ]);

            if($load){
                $drone->state_id = 2;
                $drone->save();

                $product->status = 1;
                $product->save();

                Timeline::create([
                    'title' => "Load Created",
                    'description' => "New load has been assigned",
                    'type' => 'load',
                    'type_id' => $load->id
                ]);

                Timeline::create([
                    'title' => "Drone loaded",
                    'description' => "Drone has been booked.",
                    'type' => 'drone',
                    'type_id' => $drone->id
                ]);

                Timeline::create([
                    'title' => "Product Loaded",
                    'description' => "Product {$product->title} has been shipped.",
                    'type' => 'product',
                    'type_id' => $product->id
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Drone is been loaded...'
            ]);
        }catch(Throwable $throw){
            return response()->json([
                'status' => false,
                'message' => $throw->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Loader  $loader
     * @return \Illuminate\Http\Response
     */
    public function show(Loader $loader)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Loader  $loader
     * @return \Illuminate\Http\Response
     */
    public function edit(Loader $loader)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Loader  $loader
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Loader $loader)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Loader  $loader
     * @return \Illuminate\Http\Response
     */
    public function destroy(Loader $loader)
    {
        //
    }

    public function check_drone(Drone $drone){

        $drone_status = $drone->load('drone_state', 'drone_model');
        switch ($drone_status) {
            case $drone_status->drone_state->id == 2:
                $info = "Drone is currently LOADING.";
                $status = 2;
                break;
            case $drone_status->drone_state->id == 3:
                $info = "Drone is currently LOADED.";
                $status = 3;
                break;
            case $drone_status->drone_state->id == 4:
                $info = "Drone is currently DELIVERING.";
                $status = 4;
                break;
            case $drone_status->drone_state->id == 5:
                $info = "Drone is currently DELIVERED.";
                $status = 5;
                break;
            case $drone_status->drone_state->id == 6:
                $info = "Drone is currently RETURNING.";
                $status = 6;
                break;
            default:
                $info = "Drone is still IDLE";
                $status = 1;
                break;
        }

        $product = Loader::where([['drone_id', $drone->id], ['status', $status]])->with('product', 'drone', 'customer', 'vendor')->first();

        return response()->json([
            'status' => true,
            'message' => $info,
            'data' => $product
        ]);

    }
}
