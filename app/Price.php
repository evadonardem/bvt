<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $fillable = [ 'datetime_posted', 'unit_price', 'user_id' ];

    public function user() {
    	return $this->belongsTo('App\User');
    }

    public function product() 
    {
    	return $this->belongsTo('App\Product');
    }
}
