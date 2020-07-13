<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MonsterSegment extends Model
{
    protected $table = 'monster_segments';

    protected $with = array('creator');

    public function creator()
    {
        return $this->hasOne('App\User', 'id', 'created_by');
    }
}
