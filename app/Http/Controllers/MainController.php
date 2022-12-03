<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MainController extends Controller
{
    public function home()
    {
        $title = "خانه";
        return view('main.home',compact('title'));
    }
}
