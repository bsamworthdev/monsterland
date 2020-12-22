<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\MonsterTrait;
use Carbon\Carbon;

class Monster extends Model
{
    use MonsterTrait;
    use HasFactory;

    protected $table = 'monsters';
    protected $with = array('segments', 'ratings');
    protected $appends = array('created_at_tidy','level');

    public function getCreatedAtTidyAttribute()
    {
        return Carbon::parse($this->created_at)->diffForHumans();
    }

    public function linkedUsers()
    {
        //Users linked to this monsters (i.e. has contributed to or commented on it)
        return $this->belongsToMany('App\Models\User', 'user_linked_monsters');
    }

    public function getLevelAttribute()
    {
        if ($this->vip){
            return 'Pro';
        } else {
            if ($this->auth){
                return 'Standard';
            } else {
                return 'Basic';
            }
        }
    }
}
