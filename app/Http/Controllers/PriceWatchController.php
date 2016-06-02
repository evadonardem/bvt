<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Jenssegers\Agent\Agent;
use App\Product;

class PriceWatchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $today = \Carbon\Carbon::now();

        if($request->ajax()) {    
            if($request->has('keys')) {
                $products = Product::whereIn('id', $request->get('keys'))
                ->with(['prices' => function($query) use ($today) {
                    $query->where('datetime_posted', '>=', $today->format('Y-m-d 00:00:00'));
                    $query->where('datetime_posted', '<=', $today->format('Y-m-d 23:59:59'));
                    $query->orderBy('datetime_posted', 'desc');
                    $query->orderBy('id', 'desc');
                }])->orderBy('name', 'asc')->get();                
            } else {
                $products = Product::with(['prices' => function($query) use ($today) {
                    $query->where('datetime_posted', '>=', $today->format('Y-m-d 00:00:00'));
                    $query->where('datetime_posted', '<=', $today->format('Y-m-d 23:59:59'));
                    $query->orderBy('datetime_posted', 'desc');
                    $query->orderBy('id', 'desc');
                }])->orderBy('name', 'asc')->get();
            }

            foreach($products as $product) {                
                $product->unit_price_min = number_format($product->prices->min('unit_price'), 2);
                $product->unit_price_max = number_format($product->prices->max('unit_price'), 2);
                $product->unit_price_avg = number_format($product->prices->avg('unit_price'), 2);
                $product->unit_price_latest = (count($product->prices)>0) ?  $product->prices->first() : null;
            }

            return $products;
        }

        $agent = new Agent();

        if($agent->isMobile()) {
            return redirect('mobile#pricewatch');
        } 

        return view('pricewatch.index');
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
        //
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
        //
    }
}
