<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    //
    protected $table = 'private_groups';
    protected $with = array('monsters');
    protected $appends = array('created_at_date');

    public function monsters()
    {
        return $this->hasMany('App\Models\Monster', 'group_id', 'id');
    }

    public function lastEditedMonster()
    {
        return $this->monsters()
            ->orderBy('updated_at', 'desc')
            ->first();
    }

    public function completedMonsters()
    {
        return $this->hasMany('App\Models\Monster', 'group_id', 'id')
            ->where('status','complete');
    }

    public function getCreatedAtDateAttribute()
    {
        return date( 'jS M Y', strtotime($this->created_at));
    }
}
