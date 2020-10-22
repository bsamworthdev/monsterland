<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $table = 'ratings';

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id')
            ->select(['id', 'name', 'vip']);
    }

    public function monster()
    {
        return $this->belongsTo('App\Models\Monster', 'id', 'monster_id');
    }
}
