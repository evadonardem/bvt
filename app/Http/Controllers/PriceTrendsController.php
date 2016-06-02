<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Product;

class PriceTrendsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        set_time_limit(0);

        if($request->ajax()) {
            $isMonthly = false;
            if($request->has('start') && $request->has('end')) {
                $start = \Carbon\Carbon::createFromFormat('D M d Y', $request->get('start'))->startOfMonth();
                $end = \Carbon\Carbon::createFromFormat('D M d Y', $request->get('end'))->endOfMonth();
                $isMonthly = true;
            }
                        
            $data = [];
            while($start<=$end) {
                $data[] = [ 'month' => $start->format('Y M'), 'vegetables' => [] ];
                $start->endOfMonth()->addDay();
            }

            if($request->has('keys')) {
                foreach ($data as &$rec) {
                    $start = \Carbon\Carbon::createFromFormat('Y M d', $rec['month'].' 1');
                    $end = \Carbon\Carbon::createFromFormat('Y M d', $rec['month'].' 1')->endOfMonth();
                    $products = Product::whereIn('id', $request->get('keys'))
                    ->with(['prices' => function($query) use ($start, $end) {
                        $query->where('datetime_posted', '>=', $start->format('Y-m-d H:i:s'));
                        $query->where('datetime_posted', '<=', $end->format('Y-m-d H:i:s'));
                        $query->orderBy('datetime_posted', 'desc');
                        $query->orderBy('id', 'desc');
                    }])->orderBy('name', 'asc')->get();

                    foreach ($products as &$product) {
                        //$product->unit_price_min = number_format($product->prices->min('unit_price'), 2);
                        //$product->unit_price_max = number_format($product->prices->max('unit_price'), 2);
                        $product->unit_price_avg = number_format($product->prices->avg('unit_price'), 2);
                        unset($product->prices);
                    }

                    $rec['vegetables'] = $products;
                }
            }            
                
            return $data;
        }

        return view('pricetrends.index');
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
