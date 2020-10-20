<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrophyType extends Model
{
    protected $table = 'trophy_types';
    
    public function trophies()
    {
        return $this->belongsToMany('App\Models\Trophy', 'id', 'type_id');
    }
}
