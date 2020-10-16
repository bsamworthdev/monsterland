<?php

namespace App\Traits;

trait UserTrait
{

  public function ratings()
  {
      return $this->hasMany('App\Rating', 'user_id', 'id');
  }

  public function comments()
  {
      return $this->hasMany('App\Comment', 'user_id', 'id');
  }

  public function closed_info_messages()
  {
      return $this->hasMany('App\Models\InfoMessageClosed');
  }

  public function trophies()
  {
      return $this->hasMany('App\Trophy');
  }

  public function monsterSegments()
  {
      return $this->hasMany('App\MonsterSegment','created_by','id')
          ->select('created_by','monster_id','segment');
  }

  public function streak()
  {
      return $this->hasOne('App\Streak', 'user_id', 'id');
  }
}