<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Products extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_type',
        'title',
        'description',
        'weight',
        'track_id',
        'picture',
        'status'
    ];

    public function product_type(){
        return $this->belongsTo(ProductType::class);
    }

}
