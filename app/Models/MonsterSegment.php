<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonsterSegment extends Model
{
    protected $table = 'monster_segments';

    protected $with = array('creator');

    public function creator()
    {
        return $this->hasOne('App\Models\User', 'id', 'created_by')
            ->select(['id', 'name', 'vip', 'needs_monitoring']);
    }

    public function monster()
    {
        return $this->belongsTo('App\Models\Monster', 'id', 'monster_id');
    }
}
