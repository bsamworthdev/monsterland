<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $table = 'ratings';
    protected $appends = array('created_at_date');
    protected $dates = ['created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id')
            ->select(['id', 'name', 'vip']);
    }

    public function getCreatedAtDateAttribute()
    {
        return date( 'jS M Y', strtotime($this->created_at));
    }

    public function monster()
    {
        return $this->belongsTo('App\Models\Monster', 'monster_id', 'id')
            ->select(['id', 'name']);
    }
}
