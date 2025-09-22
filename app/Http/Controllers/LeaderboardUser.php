<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LeaderboardUser extends Controller
{
    public function index()
    {
        return view('leaderboard');
    }
}
