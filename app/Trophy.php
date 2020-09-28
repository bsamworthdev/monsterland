<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Trophy extends Model
{
    protected $table = 'trophies';
    protected $with = array('trophyType');
    protected $appends = array('created_at_date');

    public function trophyType()
    {
        return $this->hasOne('App\TrophyType', 'id', 'type_id');
    }

    public function getCreatedAtDateAttribute()
    {
        return date( 'jS M Y', strtotime($this->created_at));
    }
}
