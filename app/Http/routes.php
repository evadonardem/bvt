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

	/* FOR TESTING */
	Route::get('faker/{offset_date}/{instances}', function($offset_date, $instances) {
		$offset_date = \Carbon\Carbon::createFromFormat('Y-m-d', $offset_date);
		$products = App\Product::all();        
        $faker = Faker\Factory::create();         
        foreach($products as $product) {
            for($i=0; $i<$instances; $i++) {
                $product->prices()->create([
                    'datetime_posted' => $offset_date->format('Y-m-d') . ' ' . $faker->time($format = 'H:i:s', $max = 'now'),
                    'unit_price' => $faker->randomFloat(2, 10, 500)
                ]);
            }
        }
	});

});


