<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrophyType extends Model
{
    protected $table = 'trophy_types';
    
    public function trophies()
    {
        return $this->belongsToMany('App\Trophy', 'id', 'type_id');
    }
}
