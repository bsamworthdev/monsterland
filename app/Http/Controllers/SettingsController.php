<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\ProfilePic;
use App\Models\Permission;
use App\Repositories\DBUserRepository;
use App\Repositories\DBSettingsRepository;
use Illuminate\Support\Facades\Log;
use App\Services\RedisService;


class SettingsController extends Controller
{

    protected $DBUserRepo;

    public function __construct(DBUserRepository $DBUserRepo, 
        DBSettingsRepository $DBSettingsRepo,
        RedisService $RedisService)
    {
        $this->DBUserRepo = $DBUserRepo;
        $this->DBSettingsRepo = $DBSettingsRepo;
        $this->RedisService = $RedisService;
    }

    public function index(){
        if (Auth::check()){
            $user_id = Auth::User()->id;
            $user = $this->DBUserRepo->find($user_id,['permissions']);
            $redis_activated = $this->DBSettingsRepo->redisIsActive();
            return view('settings', [
                'user_id' => $user->id,
                'is_patron' => $user->is_patron,
                'allow_NSFW' => $user->allow_nsfw,
                'allow_monster_emails'=> isset($user->permissions) ? $user->permissions->allow_monster_emails : 0,
                'peek_view_activated' => $user->peek_view,
                'follower_notify' => $user->follower_notify,
                'redis_activated' => $redis_activated ? 1 : 0
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
            // } elseif ($action == 'saveToRedis'){
            //     if ($user_id != 1) return;
            //     Redis::set('redis_test', Carbon::now());
            // } elseif ($action == 'fetchFromRedis'){
            //     if ($user_id != 1) return;
            //     $resp = Redis::get('redis_test');
            //     return $resp;
            } elseif ($action == 'flushRedis'){
                if ($user_id != 1) return;
                $this->RedisService->flushDB();
                return 'success';
            }
            
        // }
    }

    public function save(Request $request){
        $allow_monster_emails = $request->allow_monster_emails;
        $allow_NSFW = $request->allow_NSFW;
        $peek_view_activated = $request->peek_view_activated;
        $follower_notify = $request->follower_notify;
        $redis_activated = $request->redis_activated;

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
                    'allow_nsfw' => $allow_NSFW,
                    'peek_view' => $peek_view_activated,
                    'follower_notify' => $follower_notify
                ]);

            if ($user_id == 1){
                if ($redis_activated){
                    $this->DBSettingsRepo->activateRedis();
                } else {
                    $this->DBSettingsRepo->deactivateRedis();
                }
                    
            }
        }
    }
}
