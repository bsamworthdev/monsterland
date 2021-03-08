<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Peek;

class MonsterSegment extends Model
{
    use HasFactory;

    protected $table = 'monster_segments';
    protected $with = array('creator');
    protected $appends = array('peekUsed');

    public function creator()
    {
        return $this->hasOne('App\Models\User', 'id', 'created_by')
            ->select(['id', 'name', 'vip', 'needs_monitoring']);
    }

    // public function userPeeks() {
    //     return $this->hasMany('App\Models\Peek', 'user_id', 'created_by');
    // }
    
    // public function monsterPeeks() {
    //     return $this->hasMany('App\Models\Peek', 'monster_id', 'monster_id');
    // }

    public function peeksUsed() {
        return Peek::where(function($q) {
            $q->where('user_id',$this->created_by)
                ->where('monster_id',$this->monster_id);
        });
    }
    
    public function getPeekUsedAttribute() {
        // return $this->userPeeks->merge($this->monsterPeeks)->count() > 0 ? 1 : 0;
        return $this->peeksUsed()->count() > 0 ? 1 : 0;
    }


    public function monster()
    {
        return $this->belongsTo('App\Models\Monster', 'id', 'monster_id');
    }
}
