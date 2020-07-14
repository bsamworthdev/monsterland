<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $table = 'ratings';

    public function users()
    {
        return $this->belongsTo('App\User', 'id', 'user_id');
    }

    public function monsters()
    {
        return $this->belongsTo('App\Monster', 'id', 'monster_id');
    }
}
