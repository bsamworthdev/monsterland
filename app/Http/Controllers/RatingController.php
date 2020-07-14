<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Rating;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function save(Request $request)
    {
        $user_id = Auth::User()->id;
        $monster_id = $request->monster_id;
        $this_rating = $request->rating;

        $rating = new Rating;
        $rating->user_id = $user_id;
        $rating->monster_id = $monster_id;
        $rating->rating = $this_rating;
        $rating->save();
        
        return 'saved';
    }
}
