<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AuditAction extends Model
{

    protected $table = 'audit';
    protected $fillable = ['user_id','monster_id','type','action'];
    protected $dates = ['created_at', 'updated_at'];
    protected $appends = array('created_at_tidy');
    protected $with = array('monster','user');

    public function getCreatedAtTidyAttribute()
    {
        return Carbon::parse($this->created_at)->diffForHumans();
    }

    public function monster()
    {
        return $this->hasOne('App\Models\Monster','id','monster_id')
            ->select(['id', 'name', 'nsfw', 'group_id']);
    }

    public function user()
    {
        return $this->hasOne('App\Models\User','id','user_id')
            ->select(['id', 'name']);
    }
}
