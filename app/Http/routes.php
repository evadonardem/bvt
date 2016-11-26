<?php

use Jenssegers\Agent\Agent;

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return redirect('home');
});

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {	
	
	/* HOME */
	Route::resource('home', 'HomeController');

	/* PRICE WATCH */
	Route::resource('pricewatch', 'PriceWatchController');

	/* PRICE TRENDS */
	Route::resource('pricetrends', 'PriceTrendsController');

	/* DASHBOARD EXCLUSIVE FOR ADMINISTRATORS */
	Route::group(['prefix' => 'dashboard'], function() {
		Route::get('/', ['middleware' => 'auth', function() {
			return view('dashboard.index');
		}]);
		Route::resource('products', 'ProductsController');		
		Route::post('productnameunique', ['uses' => 'ProductsController@productNameUnique']);	
		Route::resource('products.prices', 'ProductsPricesController');
	});	

	/* MOBILE VIEW */
	Route::get('mobile', function() {		
		return view('mobile.index');
	});

	Route::post('mobile-authenticate', function() {
		$email = Request::input('email');
		$password = Request::input('password');

		if(Auth::attempt(['email' => $email, 'password' => $password]))	{
			return ['authentication' => 'SUCCESS'];
		}	
		return ['authentication' => 'FAILED'];
	});

	Route::get('mobile-sign-out', function() {
		Auth::logout();
	});	

	Route::get('currentdatetime', function() {
		return date('Y-m-d H:i:s');
	});


	/* AUTHENTICATION */
	Route::get('login', function() {
		if(!Auth::guest()) {
			return redirect('dashboard');
		}	
		return view('auth.log-in');
	});	
	Route::post('authenticate', function() {
		$email = Request::input('email');
		$password = Request::input('password');

		if(Auth::attempt(['email' => $email, 'password' => $password]))	{
			return redirect('dashboard');
		}	
		return redirect('login');
	});
	Route::get('logout', function() {
		Auth::logout();
		return redirect('login');
	});

	/* FOR TESTING */
	Route::get('install', function() {
		App\User::create([
			'name' => 'Rowena G. Tello',
			'email' => 'rdgtell@gmail.com',
			'password' => Hash::make('123456')
		]);
	});


	Route::get('peke', function() {
		return view('faker.index');
	});

	Route::post('peke', function() {
		$product = App\Product::find(Request::input('searchKey'));
		$start = \Carbon\Carbon::createFromFormat('Y M', Request::input('start'))->startOfMonth();
		$end = \Carbon\Carbon::createFromFormat('Y M', Request::input('end'))->endOfMonth();
		$lowest = Request::input('lowest');
		$highest = Request::input('highest');

		$faker = Faker\Factory::create(); 
		$instances = 5;

		while($start<$end) {
			for($i=0; $i<$instances; $i++) {
				$product->prices()->create([
	                'datetime_posted' => $start->format('Y-m-d') . ' ' . $faker->time($format = 'H:i:s', $max = 'now'),
	                'unit_price' => $faker->randomFloat(2, $lowest, $highest),
	                'user_id' => (Auth::guest()) ? null : Auth::user()->id
	            ]);
			}
			$start->addDay();
		}

		return redirect(url('peke'))->with(['message' => 'Fake records inserted successfully.']);
	});


	Route::post('vegetabledailypricetrend', function() {
		$vegetables = Request::input('vegetables');
		$startDate = \Carbon\Carbon::now()->startOfMonth();
        $endDate = \Carbon\Carbon::now()->endOfMonth();
        $products = App\Product::whereIn('id', $vegetables)
        ->with(['prices' => function($query) use ($startDate, $endDate) {
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
        return $products;
	});

});


