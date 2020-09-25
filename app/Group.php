<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    //
    protected $table = 'private_groups';
    protected $with = array('monsters');
    protected $appends = array('created_at_date');

    public function monsters()
    {
        return $this->hasMany('App\Monster', 'group_id', 'id');
    }

    public function getCreatedAtDateAttribute()
    {
        return date( 'jS M Y', strtotime($this->created_at));
    }
}
