<?php

namespace App\Traits;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

    public function myNotifications()
    {
        $date = $this->last_viewed_notifications_at ? : Carbon::now()->subWeeks(4)->toDateTimeString();
        //Get recent relevant notifications
        $resp = $this->belongsToMany('App\Models\AuditAction', 'user_linked_monsters', 'user_id', 'monster_id', null, 'monster_id')
            ->whereNotIn('audit.type',['rating','segment_completed'])
            ->where('audit.user_id','<>',$this->id);

        //Flag notifications that have been viewed already    
        $resp = $resp->leftJoin('notifications_closed', function($join)
        {
            $join->on('audit.id', '=', 'notifications_closed.audit_id');
            $join->on('user_linked_monsters.user_id','=', 'notifications_closed.user_id');
        })
        ->select([
            'audit.id',
            DB::Raw('not isnull(notifications_closed.user_id) as closed'),
            DB::Raw('audit.created_at > "'.$date.'" as newSinceLastVisit'),
            'audit.monster_id',
            'audit.type',
            'audit.action',
            'audit.user_id',
            'audit.created_at'])
        ->distinct('audit.id')
        ->orderBy('audit.created_at','desc')
        ->limit(10);

        return $resp;
    }
    
}