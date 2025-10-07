<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Joke;

class StaticPages extends Controller
{
    public function welcome()
    {
        $randomJoke = Joke::inRandomOrder()->first();
        return view('static/welcome', ['randomJoke' => $randomJoke]);
    }

    public function about()
    {
        return view('static/about');
    }
    public function privacy()
    {
        return view('static/privacy');
    }
    public function contact()
    {
        return view('static/contact-us');
    }
}
