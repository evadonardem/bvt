<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Product;
use App\Price;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
    	if($request->ajax()) {
    		// (start) PriceWatch Products
    		$products = Product::with(['prices' => function($query) {
	            $query->where('datetime_posted', '>=', date('Y-m-d 00:00:00'));
	            $query->where('datetime_posted', '<=', date('Y-m-d 23:59:59'));
	            $query->orderBy('datetime_posted', 'desc');
	        }])->orderBy('name', 'asc')->get();
	        foreach ($products as &$product) {
                $product->latest_price = (count($product->prices)>0) ? $product->prices[0]->unit_price : null;
                $product->latest_price_datetime_posted = (count($product->prices)>0) ? $product->prices[0]->datetime_posted : null;                
            }
            $priceWatchProducts = $products->filter(function($product) {
            	return $product->latest_price != "";
            })->shuffle()->take(4);
            // (end) PriceWatch Products

            // (start) PriceTrends Products
            $startDate = \Carbon\Carbon::now()->startOfMonth();
            $endDate = \Carbon\Carbon::now()->endOfMonth();
            $products = Product::with(['prices' => function($query) use ($startDate, $endDate) {
	            $query->where('datetime_posted', '>=', $startDate->format('Y-m-d 00:00:00'));
	            $query->where('datetime_posted', '<=', $endDate->format('Y-m-d 23:59:59'));
	            $query->orderBy('datetime_posted', 'desc');
	        }])->orderBy('name', 'asc')->get();            

            while($startDate<=$endDate) {
            	foreach ($products as &$product) {
            		$prices = $product->prices->filter(function($price) use ($startDate) {
            			return $price->datetime_posted >= $startDate->format('Y-m-d 00:00:00') && $price->datetime_posted <= $startDate->format('Y-m-d 23:59:59');
            		});
            		$product->{$startDate->format('Y-m-d')} = $prices->avg('unit_price');
            		unset($product->prices);
            	}
            	$startDate->addDay(1);
            }
            $priceTrendsProducts = $products->shuffle()->take(2);
            // (end) PriceTrends Products

            
            $data = [
            	'priceWatch' => $priceWatchProducts,
            	'priceTrends' => $priceTrendsProducts
            ];

            return $data;
    	}

        return view('home.index');
    }
}
