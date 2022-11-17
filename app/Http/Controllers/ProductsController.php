<?php

namespace App\Http\Controllers;

use App\Models\Products;
use App\Models\Timeline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;
use Image;

class ProductsController extends Controller
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
            'data' => Products::all()
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
                'product_type' => 'required',
                'title' => 'required',
                'description' => 'required',
                'weight' => 'required|integer',
                'code' => 'required|regex:/^[\s\w-]*$/',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            if($validate->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validate->errors()
                ], 401);
            }

            if ($request->file('image')) {
                $image = $request->file('image');
                $picture = time().'.'.$image->getClientOriginalExtension();

                $destinationPath = public_path('/product_image');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 666, true);
                }

                $img = Image::make($image->getRealPath());
                $img->resize(200, 200, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($destinationPath.'/'.$picture);

                $destinationPath = public_path('/images');
                $image->move($destinationPath, $picture);
            }

            $product = Products::create([
                'product_type' => $request->product_type,
                'title' => $request->title,
                'description' => $request->description,
                'weight' => $request->weight,
                'track_id' => $request->code,
                'picture'  => '/product_image/'.$picture ?? null
            ]);

            Timeline::create([
                'title' => "Product Created",
                'description' => "New product {$product->title} has  been added",
                'type' => 'product',
                'type_id' => $product->id
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Product created successfully.',
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
     * @param  \App\Models\Products  $products
     * @return \Illuminate\Http\Response
     */
    public function show(Products $products)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Products  $products
     * @return \Illuminate\Http\Response
     */
    public function edit(Products $products)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Products  $products
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Products $products)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Products  $products
     * @return \Illuminate\Http\Response
     */
    public function destroy(Products $products)
    {
        try{
            $products->delete();
            return response()->json([
                'status' => true,
                'message' => 'Product has been removed.'
            ]);
        }catch(Throwable $throw){
            return response()->json([
                'status' => false,
                'message' => $throw->getMessage()
            ], 500);
        }
    }
}
