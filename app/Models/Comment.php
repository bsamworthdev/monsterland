<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'comments';
    protected $fillable = ['comment','votes','spam','deleted','reply_id','monster_id','user_id'];
    protected $dates = ['created_at', 'updated_at'];

    public function replies()
    {
        return $this->hasMany('App\Models\Comment','id','reply_id');
    }

}