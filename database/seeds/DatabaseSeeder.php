<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UserTableSeeder::class);
        $this->call(ProductsTableSeeder::class);
    }
}

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $vegetables = [
            'Asparagus', 
            'Baguio Beans', 
            'Pechay',
            'Broccoli',
            'Cabbage',
            'Carrots',
            'Cauliflower',
            'Celery',
            'Cucumber',
            'Lettuce',
            'Sweet Peas',
            'Chayote',
            'Potatoes',
            'Tomato',
            'Radish - Long White',
            'Korean Radish',
            'Bell Pepper',
            'Parsley',
            'Chinese Cabbage',
            'Sugar Beets',
            'Onion Leaks'
        ];

        foreach($vegetables as $vegetable) {
            App\Product::create([
                'name' => $vegetable
            ]);
        }
        
        //$products = App\Product::all();

        /*foreach($products as $product) {
            for($i=0; $i<9999; $i++) {
                $product->prices()->save(factory(App\Price::class)->make());
            }
        }*/

        /*$products = App\Product::all();

        $today = \Carbon\Carbon::now();
        $faker = Faker\Factory::create(); 
        foreach($products as $product) {
            for($i=0; $i<500; $i++) {
                $product->prices()->create([
                    'datetime_posted' => $today->format('Y-m-d') . ' ' . $faker->time($format = 'H-i-s', $max = 'now'),
                    'unit_price' => $faker->randomFloat(2, 10, 1000)
                ]);
            }
        }*/

    }
}
