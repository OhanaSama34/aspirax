<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;

class LeaderboardUser extends Controller
{
    public function index()
    {
        $leaderboard = User::orderBy('point', 'desc')->get();

        return view('leaderboard', compact('leaderboard'));
    }
}
