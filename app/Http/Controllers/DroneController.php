<?php

namespace App\Http\Controllers;

use App\Models\Drone;
use App\Models\Products;
use App\Models\Timeline;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class DroneController extends Controller
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
            'message' => 'Successful',
            'data' => Drone::with('drone_model', 'drone_state')->get()
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

            $validate = Validator::make($request->all(), [
                'serial' => 'required|max:100',
                'model' => 'required|integer',
                'weight' => 'required|numeric|max:500',
                'battery_level' => 'required',
                'state' => 'required|integer'
            ]);

            if($validate->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validate->errors()
                ], 401);
            }

            $drone = Drone::create([
                'serial_number' => $request->serial,
                'model_id' => $request->model,
                'weight_limit' => $request->weight,
                'battery_level' => $request->battery_level,
                'state_id' => $request->state
            ]);

            Timeline::create([
                'title' => "Drone created",
                'description' => "New drone has  been created",
                'type' => 'drone',
                'type_id' => $drone->id
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Drone created',
                'data' => $drone->load('drone_model', 'drone_state')
            ]);

        }catch(Throwable $throw){
            return response()->json([
                'status' => false,
                'message' => $throw->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Drone  $drone
     * @return \Illuminate\Http\Responses
     */
    public function show($drone)
    {
        $drone = Drone::where('id', $drone)->with('drone_model', 'drone_state')->first();
        return response()->json([
            'status' => true,
            'message' => 'Successful',
            'data' => $drone
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Drone  $drone
     * @return \Illuminate\Http\Response
     */
    public function edit(Drone $drone)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Drone  $drone
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Drone $drone)
    {
        try{

            $drone->serial_number = $request->serial ?? $drone->serial_number;
            $drone->model_id = $request->model ?? $drone->model_id;
            $drone->weight_limit = $request->weight ?? $drone->weight_limit;
            $drone->battery_level = $request->battery_level ?? $drone->battery_level;
            $drone->state_id = $request->state ?? $drone->state_id;

            $drone->save();

            Timeline::create([
                'title' => "Drone Updaate",
                'description' => "Drone data has been updated.",
                'type' => 'drone',
                'type_id' => $drone->id
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Drone has been updated.',
                'data' => $drone->load('drone_model', 'drone_state')
            ]);

        }catch(Throwable $throw){
            return response()->json([
                'status' => false,
                'message' => $throw->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Drone  $drone
     * @return \Illuminate\Http\Response
     */
    public function destroy(Drone $drone)
    {

        try{
            $drone->delete();
            return response()->json([
                'status' => true,
                'message' => 'Drone has been removed.'
            ]);
        }catch(Throwable $throw){
            return response()->json([
                'status' => false,
                'message' => $throw->getMessage()
            ], 500);
        }
    }



    public function battery_level(Drone $drone){

        switch ($drone->battery_level) {
            case $drone->battery_level <= 25:
                $level = "This drone is unavailable for delivery, power it up.";
                $drone->state_id = 1;
                $drone->save();
                Timeline::create([
                    'title' => "Drone State Updated",
                    'description' => "This drone is low on power and its state has been changed.",
                    'type' => 'drone',
                    'type_id' => $drone->id
                ]);
                break;
            case $drone->battery_level == 50:
                $level = "This drone has 50% battery life remaining.";
                break;
            default:
            $level = "This drone is available for delivery, load it up.";
                break;
        }
        return response()->json([
            'status' => true,
            'message' => $level,
            'data' => $drone->load('drone_state', 'drone_model')
        ]);
    }

    public function drone_state(Drone $drone){
        switch ($drone->state_id) {
            case 1:
                $level = "This drone is IDLE.";
                break;
            case 2:
                $level = "This drone is LOADING";
                break;
            case 3:
                $level = "This drone is LOADED.";
                break;
            case 4:
                $level = "This drone is DELIVERING.";
                break;
            case 5:
                $level = "This drone has DELIVERED.";
                break;
            case 6:
                $level = "This drone is RETURNING.";
                break;
            default:
            $level = "No update from this drone.";
                break;
        }
        return response()->json([
            'status' => true,
            'message' => $level,
            'data' => $drone->load('drone_state', 'drone_model')
        ]);
    }

    public function available_drones(){
        $drones = Drone::where('state_id', 1)->with('drone_model', 'drone_state')->get();
        return response()->json([
            'status' => true,
            'message' => 'Successful',
            'data' => $drones
        ]);
    }

}
