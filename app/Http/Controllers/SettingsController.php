<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\ProfilePic;
use App\Models\Permission;
use App\Repositories\DBUserRepository;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{

    protected $DBUserRepo;

    public function __construct(DBUserRepository $DBUserRepo)
    {
        $this->DBUserRepo = $DBUserRepo;
    }

    public function index(){
        if (Auth::check()){
            $user_id = Auth::User()->id;
            $user = $this->DBUserRepo->find($user_id,['permissions']);
            return view('settings', [
                'allow_NSFW' => $user->allow_nsfw,
                'allow_monster_emails'=> isset($user->permissions) ? $user->permissions->allow_monster_emails : 0
            ]);
        }
    }

    //
    public function update(Request $request){
        $action = $request->action;
        $checked = $request->checked;

        // if (Auth::check()){
            $user_id = Auth::User()->id;
            if ($action == 'nsfw_setting'){
                $user = User::find($user_id);
                $user->allow_nsfw = $checked;
                $user->save();
            } elseif ($action == 'setProfilePic'){
                $monster_id = $request->monsterId;
                ProfilePic::updateOrCreate([
                    'user_id' => $user_id
                ],[
                    'type' => 'monster',
                    'monster_id' => $monster_id
                ]);
            } elseif ($action == 'unsetProfilePic'){
                $monster_id = $request->monsterId;
                $profilePic = ProfilePic::where('user_id',$user_id);
                $profilePic->delete();
            }
            
        // }
    }

    public function save(Request $request){
        $allow_monster_emails = $request->allow_monster_emails;
        $allow_NSFW = $request->allow_NSFW;

        if (Auth::check()){
            $user_id = Auth::User()->id;

            $user = $this->DBUserRepo->find($user_id,['permissions']);
            if ($user->permissions){
                $user->permissions->allow_monster_emails = $allow_monster_emails;
                $user->permissions->save();
            } else {
                Permission::create([
                    'user_id' => $user_id, 
                    'allow_monster_emails' => $allow_monster_emails
                ]);
            } 
            
            User::where('id', $user_id)
                ->update([
                    'allow_nsfw' => $allow_NSFW
                ]);
        }
    }
}
