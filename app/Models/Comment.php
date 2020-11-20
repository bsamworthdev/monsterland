<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'comments';
    protected $fillable = ['comment','votes','spam','deleted','reply_id','monster_id','user_id'];
    protected $dates = ['created_at', 'updated_at'];
    protected $appends = array('created_at_date');
    protected $with = array('monster');

    public function replies()
    {
        return $this->hasMany('App\Models\Comment','id','reply_id');
    }

    public function getCreatedAtDateAttribute()
    {
        return date( 'jS M Y', strtotime($this->created_at));
    }

    public function monster()
    {
        return $this->hasOne('App\Models\Monster','id','monster_id')
            ->select(['id', 'name']);
    }

}