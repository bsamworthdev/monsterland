<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword; 
use Illuminate\Notifications\Notifiable;
use App\Traits\UserTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;
    use UserTrait;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'email',
    ];

    protected $with = array('streak');

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function linkedMonsters()
    {
        //Monsters this user has contributed to or commented on
        $resp= $this->belongsToMany('App\Models\Monster', 'user_linked_monsters');
        return $resp;
    }

    public function notifications()
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
        ->orderBy('audit.created_at','desc')
        ->limit(10);

        return $resp;
    }
    
}