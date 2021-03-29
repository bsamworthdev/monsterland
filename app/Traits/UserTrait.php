<?php

namespace App\Traits;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;

trait UserTrait
{
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
                return $q->whereNotIn('audit.type',['rating','segment_completed','mention','misc'])
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

        // Log::Debug($resp->toSql());
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
                ->orderBy('is_me', 'desc');
        } else {
            $notifications = $this->myDirectNotifications()->union($this->myMonsterNotifications())
                ->orderBy('created_at', 'desc');
        }
           
            
        return $notifications;
    }

    public function getMyLatestNotificationsAttribute()
    {
        $user_id = $this->id;
        if ($user_id == 1){
            if (Redis::exists($user_id.'_notifications_last_fetched') && 
                Carbon::NOW()->diffInMinutes(Redis::get($user_id.'_notifications_last_fetched')) < 5){
                $notifications = Redis::get($user_id.'_notifications');
            } else {
                //Only re-fetch notifications after 5 minutes
                $notifications = $this->myNotifications;
                Redis::set($user_id.'_notifications', $notifications);
                Redis::set($user_id.'_notifications_last_fetched', Carbon::NOW());
            }
        } else {

        $notifications = $this->myNotifications;
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