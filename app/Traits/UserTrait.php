<?php

namespace App\Traits;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;
use Illuminate\Support\Facades\Hash;
use App\Services\RedisService;
use Carbon\Carbon;

trait UserTrait
{

    protected $RedisService;

    public function __construct() 
    {
        $this->RedisService = \App::Make('App\Services\RedisService');
    }

    public function ratings()
    {
        return $this->hasMany('App\Models\Rating', 'user_id', 'id');
    }

    public function comments()
    {
        return $this->hasMany('App\Models\Comment', 'user_id', 'id');
    }

    public function closed_info_messages()
    {
        return $this->hasMany('App\Models\InfoMessageClosed');
    }

    public function trophies()
    {
        return $this->hasMany('App\Models\Trophy')
            ->orderBy('created_at','desc');
    }

    public function socialMediaAccounts()
    {
        return $this->hasMany('App\Models\SocialMediaAccount')
            ->whereNotNull('account_name')
            ->orderBy('account_type');
    }

    public function tagsAdded()
    {
        return $this->hasMany('App\Models\Tag', 'manually_added_by','id')
            ->orderBy('created_at','desc');
    }

    public function monsterSegments()
    {
        return $this->hasMany('App\Models\MonsterSegment','created_by','id')
            ->select('created_by','monster_id','segment');
    }

    public function streak()
    {
        return $this->hasOne('App\Models\Streak', 'user_id', 'id');
    }

    public function groups()
    {
        return $this->hasMany('App\Models\Group', 'created_by_user_id', 'id');
    }

    public function permissions()
    {
        return $this->hasOne('App\Models\Permission', 'user_id', 'id');
    }

    public function linkedMonsters()
    {
        //Monsters this user has contributed to or commented on
        $resp= $this->belongsToMany('App\Models\Monster', 'user_linked_monsters');
        return $resp;
    }

    public function profilePic()
    {
        $resp= $this->hasOne('App\Models\ProfilePic', 'user_id', 'id');
        return $resp;
    }

    public function myMonsterNotifications()
    {
        $date = $this->last_viewed_notifications_at ? : Carbon::now()->subWeeks(4)->toDateTimeString();
        //Get recent relevant notifications
        $resp = $this->belongsToMany('App\Models\AuditAction', 'user_linked_monsters', 'user_id', 'monster_id', null, 'monster_id')
            ->where(function ($q) {
                return $q->whereIn('audit.type',['flag', 'comment','monster_completed'])
                // return $q->whereNotIn('audit.type',['rating','segment_completed','tag', 'mention','misc'])
                ->whereRaw('audit.created_at >= user_linked_monsters.created_at')
                ->where('audit.user_id','<>',$this->id);
            });
            // ->orWhere(function ($q) {
            //     return $q->where('audit.type','mention')
            //     ->where('audit.object_user_id',$this->id)
            //     ->where('audit.user_id','<>',$this->id);
            // });
            

        //Flag notifications that have been viewed already    
        $resp = $resp->leftJoin('notifications_closed', function($join)
        {
            $join->on('audit.id', 'notifications_closed.audit_id');
            $join->on(function($query) {
                return $query->on('user_linked_monsters.user_id', 'notifications_closed.user_id')
                        ->orOn('audit.object_user_id','notifications_closed.user_id');
            });
        })
        ->select([
            'audit.id as audit_id',
            DB::Raw('not isnull(notifications_closed.user_id) as closed'),
            DB::Raw('audit.created_at > "'.$date.'" as newSinceLastVisit'),
            'audit.monster_id',
            'audit.type',
            'audit.action',
            'audit.user_id',
            'audit.created_at',
            DB::Raw('\'1\' as is_me')])
        ->distinct('audit.id')
        ->orderBy('audit.created_at','desc')
        ->limit(10);

        return $resp;
    }

    public function followedUsersMonsterNotifications()
    {
        $date = $this->last_viewed_notifications_at ? : Carbon::now()->subWeeks(4)->toDateTimeString();

        $resp = $this->hasMany('App\Models\Follow', 'follower_user_id')
                ->join('user_linked_monsters', 'user_linked_monsters.user_id', 'follows.followed_user_id')
                ->join('audit', 'audit.monster_id', 'user_linked_monsters.monster_id')
                ->where('audit.type', 'monster_completed');

        //Flag notifications that have been viewed already
        $resp = $resp->leftJoin('notifications_closed', function($join)
        {
            $join->on('audit.id', 'notifications_closed.audit_id');
            $join->on(function($query) {
                return $query->on('follows.follower_user_id', 'notifications_closed.user_id')
                        ->orOn('audit.object_user_id','notifications_closed.user_id');
            });
        })
        ->select([
            'audit.id as audit_id',
            DB::Raw('not isnull(notifications_closed.user_id) as closed'),
            DB::Raw('audit.created_at > "'.$date.'" as newSinceLastVisit'),
            'audit.monster_id as monster_id',
            DB::Raw('\'followed_user_monster_completed\' as type'),
            'audit.action',
            'follows.followed_user_id as user_id',
            'audit.created_at',
            DB::Raw('\'0\' as is_me')])
        ->orderBy('audit.created_at','desc')
        ->limit(10);

        // Log::Debug($resp->toSql());
        // Log::Debug($resp->getBindings());
        return $resp;
    }
    
    public function myDirectNotifications()
    {
        $date = $this->last_viewed_notifications_at ? : Carbon::now()->subWeeks(4)->toDateTimeString();
        //Get recent relevant notifications
        $resp = $this->hasMany('App\Models\AuditAction','object_user_id','id')
            ->where('audit.type','mention')
            ->where('audit.object_user_id',$this->id)
            ->where('audit.user_id','<>',$this->id);

        //Flag notifications that have been viewed already    
        $resp = $resp->leftJoin('notifications_closed', function($join)
        {
            $join->on('audit.id', 'notifications_closed.audit_id');
            $join->on('audit.object_user_id','notifications_closed.user_id');
        })
        ->select([
            'audit.id as audit_id',
            DB::Raw('not isnull(notifications_closed.user_id) as closed'),
            DB::Raw('audit.created_at > "'.$date.'" as newSinceLastVisit'),
            'audit.monster_id',
            'audit.type',
            'audit.action',
            'audit.user_id',
            'audit.created_at',
            DB::Raw('\'1\' as is_me')])
        ->distinct('audit.id')
        ->orderBy('audit.created_at','desc')
        ->limit(10);

        return $resp;
    }

    public function myNotifications()
    {  
        if ($this->follower_notify){
            $notifications = $this->myDirectNotifications()->union($this->myMonsterNotifications())
                ->union($this->followedUsersMonsterNotifications())
                ->orderBy('created_at', 'desc')
                ->orderBy('is_me', 'desc')
                ->limit(12);
        } else {
            $notifications = $this->myDirectNotifications()->union($this->myMonsterNotifications())
                ->orderBy('created_at', 'desc')
                ->limit(12);
        }
        return $notifications;
    }

    public function getMyLatestNotificationsAttribute()
    {
        $user_id = $this->id;
        if ($this->RedisService->exists($user_id.'_notifications_last_fetched') && 
            Carbon::NOW()->diffInMinutes($this->RedisService->get($user_id.'_notifications_last_fetched')) < 1){
            $notifications = $this->RedisService->get($user_id.'_notifications');
        } else {
            //Only re-fetch notifications after 5 minutes
            $notifications = $this->myNotifications;
            $this->RedisService->set($user_id.'_notifications', $notifications);
            $this->RedisService->set($user_id.'_notifications_last_fetched', Carbon::NOW());
        }
        
        //Unflag notifications closed in last 5 minutes
        $closed_notifications = DB::table('notifications_closed')
            ->select(['audit_id'])
            ->where('user_id',$this->id)
            ->where('created_at','>',Carbon::now()->subMinutes(5)->toDateTimeString())
            ->orderBy('created_at','desc')
            ->limit(10)
            ->get()
            ->pluck('audit_id')
            ->toArray();

        if (count($closed_notifications) > 0) {
            $notifications = json_decode($notifications);
            foreach ($notifications as $notification){
                if (in_array($notification->audit_id,$closed_notifications)){
                    $notification->closed = 1;
                }
            }  
            // Log::Debug($notifications);
            $notifications = json_encode($notifications);  
        }

        return $notifications;
    }

    public function getCanUseStoreAttribute(){
        $storeSetting = Setting::where('name','store_setting')->first();
        if ($storeSetting){
            switch ($storeSetting->value){
                case 'admin_only':
                    return ($this->id==1);
                    break;
                case 'patron_only':
                    return $this->is_patron;
                    break;
                case 'moderator_only':
                    return ($this->moderator || $this->is_patron);
                    break;
                case 'vip_only':
                    return ($this->vip || $this->moderator || $this->is_patron);
                    break;
                case 'member_only':
                    return ($this != NULL);
                    break;
                case 'everyone':
                    return true;
                    break;
                default:
                    return false;
            }
        } else {
            return false;
        }
    }

    public function followingUsers()
    {
        return $this->hasMany('App\Models\Follow', 'follower_user_id', 'id');
    }

    public function followedByUsers()
    {
        return $this->hasMany('App\Models\Follow', 'followed_user_id', 'id');
    }
}