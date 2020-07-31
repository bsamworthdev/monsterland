<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use app\User;

class SettingsController extends Controller
{
    //
    public function update(Request $request){
        $action = $request->action;
        $checked = $request->checked;

        // if (Auth::check()){
            if ($action == 'nsfw_setting'){
                $user_id = Auth::User()->id;

                $user = User::find($user_id);
                $user->allow_nsfw = $checked;
                $user->save();
            }
        // }
    }
}
