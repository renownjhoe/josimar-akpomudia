<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Drone extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'serial_number',
        'model_id',
        'weight_limit',
        'battery_level',
        'state_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'model_id',
        'state_id',
    ];

    public function drone_model(){
        return $this->belongsTo(DroneModel::class, 'model_id');
    }

    public function drone_state(){
        return $this->belongsTo(DroneState::class,'state_id');
    }
}
