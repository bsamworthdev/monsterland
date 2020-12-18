<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\DBMonsterRepository;

class WelcomeController extends Controller
{

    protected $DBMonsterRepo;

    public function __construct(Request $request, 
        DBMonsterRepository $DBMonsterRepo)
    {
        $this->DBMonsterRepo = $DBMonsterRepo;
    }

    public function index()
    {

        $monsters = $this->DBMonsterRepo->getFeaturedMonsters()->toArray();

        $rand_key = array_rand($monsters);
        $monster = $monsters[$rand_key];
        
        return view('welcome', [
            "monster" => $monster,
        ]);
    }
}
