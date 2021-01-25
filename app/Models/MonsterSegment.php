<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MonsterSegment extends Model
{
    use HasFactory;

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
