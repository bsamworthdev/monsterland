<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $table = 'ratings';

    public function users()
    {
        return $this->belongsTo('App\Models\User', 'id', 'user_id');
    }

    public function monsters()
    {
        return $this->belongsTo('App\Models\Monster', 'id', 'monster_id');
    }
}
