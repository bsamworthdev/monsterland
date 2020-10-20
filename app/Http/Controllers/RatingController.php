<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\DBRatingRepository;

class RatingController extends Controller
{

    protected $DBRatingRepo;
    protected $user_id;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request, 
        DBRatingRepository $DBRatingRepo)
    {
        $this->middleware(['auth','verified', function($request, $next) 
            use ($DBRatingRepo){

            $this->DBRatingRepo = $DBRatingRepo;
            $this->user_id = Auth::User()->id;
            return $next($request);
        }]); 
        
    }

    public function save(Request $request)
    {
        $monster_id = $request->monster_id;
        $rating = $request->rating;

        $this->DBRatingRepo->saveRating($this->user_id, $monster_id, $rating);
        
        return 'saved';
    }
}
