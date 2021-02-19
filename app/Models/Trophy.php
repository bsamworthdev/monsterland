<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trophy extends Model
{
    protected $table = 'trophies';
    protected $with = array('trophyType');
    protected $appends = array('created_at_date','default_description_html');

    public function trophyType()
    {
        return $this->hasOne('App\Models\TrophyType', 'id', 'type_id');
    }

    public function getCreatedAtDateAttribute()
    {
        return date( 'jS M Y', strtotime($this->created_at));
    }

    public function getDefaultDescriptionHtmlAttribute()
    {
        return htmlentities($this->default_description);
    }
}
