<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Monster extends Model
{
    protected $table = 'monsters';

    protected $with = array('segments');

    public function segments()
    {
        return $this->hasMany('App\MonsterSegment');
    }
}
