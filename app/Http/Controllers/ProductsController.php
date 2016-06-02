<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Product;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $products = Product::with(['prices' => function($query) {
            $query->where('datetime_posted', '>=', date('Y-m-d 00:00:00'));
            $query->where('datetime_posted', '<=', date('Y-m-d 23:59:59'));
            $query->orderBy('datetime_posted', 'desc');
        }])->orderBy('name', 'asc')->get();
        if($request->ajax()) {
            foreach ($products as &$product) {
                $product->latest_price = (count($product->prices)>0) ? $product->prices[0]->unit_price : '-';
                $product->add_unit_price_url = action('ProductsPricesController@store', [$product->id]);
                $product->price_history_url = action('ProductsPricesController@index', [$product->id]);
                $product->delete_url = action('ProductsController@destroy', $product->id);
            }
            return $products;
        }

        return view('products.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $product = Product::create($request->all());
        $product->add_unit_price_url = action('ProductsPricesController@store', [$product->id]);
        $product->price_history_url = action('ProductsPricesController@index', [$product->id]);
        $product->delete_url = action('ProductsController@destroy', $product->id);
        return $product;      
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->add_unit_price_url = action('ProductsPricesController@store', [$product->id]);
        $product->price_history_url = action('ProductsPricesController@index', [$product->id]);
        $product->delete_url = action('ProductsController@destroy', $product->id);      
        $product->delete();
        return $product;
    }


    public function productNameUnique(Request $request) {
        $name = $request->input('name');
        $product = Product::where('name', '=', $name)->first();
        return (is_object($product)) ? $product : [];
    }

}
