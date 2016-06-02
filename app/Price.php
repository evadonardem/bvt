<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $fillable = [ 'datetime_posted', 'unit_price' ];

    public function product() 
    {
    	return $this->belongsTo('App\Product');
    }
}
