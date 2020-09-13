<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Group;
use Illuminate\Support\Facades\Log;
use App\Session;
use Illuminate\Support\Facades\Redirect;

class GroupController extends Controller
{

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function index(Request $request){ 

        $name = $request->name;
        $group_code = $request->group_code;  

        // // Log::Debug('test'.$group_code);
        $group = Group::where('code', $group_code)->get(['id', 'name'])->first();

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
