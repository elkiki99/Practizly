<?php

namespace App\Http\Controllers;

class UserDashboard extends Controller
{
    public function dashboard()
    {
        return view('user.dashboard');
    }

    public function profile()
    {
        return view('user.profile');
    }

    public function calendar()
    {
        return view('user.calendar');
    }

    public function subjects()
    {
        return view('user.subjects');
    }
    
    public function quizzes()
    {
        return view('user.quizzes');
    }
        
    public function exam()
    {
        return view('user.exam');
    }
    
    public function summaries()
    {
        return view('user.summaries');
    }

    public function settings()
    {
        return view('user.settings');
    }
}
