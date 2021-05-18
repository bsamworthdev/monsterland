<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\DBSocialMediaAccountsRepository;

class SocialMediaAccountsController extends Controller
{
    protected $DBSocialMediaAccountsRepo;

    public function __construct(Request $request, 
        DBSocialMediaAccountsRepository $DBSocialMediaAccountsRepo)
    {
        $this->middleware(['auth','verified']);
        $this->DBSocialMediaAccountsRepo = $DBSocialMediaAccountsRepo;
    }

    public function update(Request $request)
    {
        if (Auth::check()){
            $user_id = Auth::User()->id;
            $accounts = $request->accounts;
            $this->DBSocialMediaAccountsRepo->update($user_id, $accounts);
        }
        return redirect()->back()->with('message', 'saved');
    }
}
