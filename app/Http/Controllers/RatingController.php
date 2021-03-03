<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\DBRatingRepository;
use App\Repositories\DBAuditRepository;

class RatingController extends Controller
{

    protected $DBRatingRepo;
    protected $DBAuditRepo;
    protected $user_id;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request, 
        DBRatingRepository $DBRatingRepo,
        DBAuditRepository $DBAuditRepo)
    {
        $this->middleware(['auth','verified', function($request, $next) 
            use ($DBRatingRepo, $DBAuditRepo){

            $this->DBRatingRepo = $DBRatingRepo;
            $this->DBAuditRepo = $DBAuditRepo;
            $this->user_id = Auth::User()->id;
            return $next($request);
        }]); 
        
    }

    public function save(Request $request)
    {
        $monster_id = $request->monster_id;
        $rating = $request->rating;
        $user_id = $this->user_id;

        if (!$this->DBRatingRepo->hasRated($user_id, $monster_id)){
            $this->DBRatingRepo->saveRating($user_id, $monster_id, $rating);
        }
        
        //Audit
        $this->DBAuditRepo->create($user_id, $monster_id, 'rating', ' was rated '.$rating);

        return 'saved';
    }
}
