<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Loader extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "vendor",
        "customer",
        "drone_id",
        "product_id",
        "status"
    ];

    public function vendor(){
        return $this->belongsTo(User::class, 'vendor', 'id');
    }

    public function customer(){
        return $this->belongsTo(User::class, 'customer', 'id');
    }

    public function drone(){
        return $this->belongsTo(Drone::class);
    }

    public function product(){
        return $this->belongsTo(Products::class);
    }
}
