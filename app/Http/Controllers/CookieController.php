<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cookie;
use Response;

class CookieController extends Controller
{
    public function set(){
    	try {
    		$minutes = 2628000;
      	    Cookie::queue(Cookie::make('menu_expand', 'yes', $minutes));
    	} catch (\Exception $e) {
    		throw new Exception("Error " . $e->getMessage(), 1);
    	}
    }

    public function unset(){
    	try {
    		Cookie::queue(Cookie::forget('menu_expand'));
    	} catch (\Exception $e) {
    		throw new Exception("Error " . $e->getMessage(), 1);
    	}
    }
}
