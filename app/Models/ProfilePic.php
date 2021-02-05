<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfilePic extends Model
{
    use HasFactory;

    protected $table = 'user_profile_pics';
    protected $fillable = ['user_id','type','id','monster_id'];
    protected $appends = ['monster_nsfw'];

    public function monster()
    {
        return $this->belongsTo('App\Models\Monster', 'monster_id')
            ->select(['nsfw']);
    }

    public function getMonsterNSFWAttribute()
    {
        return $this->monster->nsfw;
    }
}
