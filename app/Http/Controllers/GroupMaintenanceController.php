<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Group;
use Illuminate\Support\Facades\Auth;

class GroupMaintenanceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }

    public function index(Request $request){ 

        // $name = $request->name;
        // $group_code = $request->group_code;  

        // $group = Group::where('code', $group_code)->get(['id', 'name'])->first();

        // if ($group !== null){
        //     $request->session()->put('group_username', $name);
        //     $request->session()->put('group_id', $group['id']);
        //     $request->session()->put('group_name', $group['name']);
        //     return redirect('nonauth/home');
        // } else {
        //     return Redirect::back()->withErrors('Group Code not recognised');
        // }

        $user_id = Auth::User()->id;
        $user = User::find($user_id);

        $groups = Group::where('created_by_user_id', $user_id)
            ->orderBy('created_at','desc')
            ->get();

        return view('groupMaintenance', [
            'groups' => $groups
        ]);
    }

    public function create(Request $request)
    {
        $group = new Group;

        $user_id = Auth::User()->id;
        $user = User::find($user_id);

        $name = trim($request->name);
        if ($name == "" || strlen($name) > 20){
            die();
        }

        $group->name = $name;
        $group->created_by_user_id = $user_id;

        $code = $this->generateRandomString();
        $group->code = $code;

        // $profanity = Profanity::whereRaw('"'.$name.'" like CONCAT("%", word, "%")')
        //     ->orderBy('nsfl','desc')
        //     ->orderBy('nsfw','desc')
        //     ->get();

        // if (count($profanity) > 0) {
        //     if ($profanity[0]->nsfw){
        //         $monster->nsfw = 1;
        //     }
        //     if ($profanity[0]->nsfl){
        //         $monster->nsfl = 1;
        //     }
        // }

        $group->save();

        header('Location: /privategroups');
        die();

        //header("Refresh:0");
    }

    function generateRandomString($length = 8) {
        $characters = '23456789abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
