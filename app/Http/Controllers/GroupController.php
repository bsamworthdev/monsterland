<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;
use Illuminate\Support\Facades\Log;
use App\Models\Session;
use Illuminate\Support\Facades\Redirect;
use App\Repositories\DBGroupRepository;

class GroupController extends Controller
{

    protected $DBGroupRepository;

    public function __construct(DBGroupRepository $DBGroupRepository)
    {
        // $this->middleware('guest');
        $this->DBGroupRepository = $DBGroupRepository;
    }

    public function index(Request $request){ 

        $name = $request->name;
        $group_code = $request->group_code;  

        $group = $this->DBGroupRepository->getGroupByCode($group_code);

        if ($group !== null){
            $request->session()->put('group_username', $name);
            $request->session()->put('group_id', $group['id']);
            $request->session()->put('group_name', $group['name']);
            return redirect('nonauth/home');
        } else {
            return Redirect::back()->withErrors('Group Code not recognised');
        }
    }
}
