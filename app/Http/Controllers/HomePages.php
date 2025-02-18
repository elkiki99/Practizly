<?php

namespace App\Http\Controllers;

class HomePages extends Controller
{
    public function welcome()
    {
        return view('homepages.welcome');
    }

    public function contact()
    {
        return view('homepages.contact');
    }

    public function docs()
    {
        return view('homepages.docs');
    }

    public function blog()
    {
        return view('homepages.blog');
    }

    public function clients()
    {
        return view('homepages.clients');
    }

    public function pricing()
    {
        return view('homepages.pricing');
    }
    
    public function terms()
    {
        return view('homepages.terms');
    }

    public function privacy()
    {
        return view('homepages.privacy');
    }
}
