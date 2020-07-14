<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Monster extends Model
{
    protected $table = 'monsters';

    protected $with = array('segments', 'ratings');

    public function segments()
    {
        return $this->hasMany('App\MonsterSegment');
    }

    public function ratings()
    {
        return $this->hasMany('App\Rating', 'monster_id', 'id');
    }
}
