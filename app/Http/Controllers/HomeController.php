<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Jenssegers\Agent\Agent;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $agent = new Agent();

        if($agent->isMobile()) {
            return redirect('mobile#home');
        } 

        return view('home.index');
    }
}
