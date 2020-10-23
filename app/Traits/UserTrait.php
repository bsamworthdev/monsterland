<?php

namespace App\Traits;

trait UserTrait
{

  public function ratings()
  {
      return $this->hasMany('App\Models\Rating', 'user_id', 'id');
  }

  public function comments()
  {
      return $this->hasMany('App\Models\Comment', 'user_id', 'id');
  }

  public function closed_info_messages()
  {
      return $this->hasMany('App\Models\InfoMessageClosed');
  }

  public function trophies()
  {
      return $this->hasMany('App\Models\Trophy');
  }

  public function monsterSegments()
  {
      return $this->hasMany('App\Models\MonsterSegment','created_by','id')
          ->select('created_by','monster_id','segment');
  }

  public function streak()
  {
      return $this->hasOne('App\Models\Streak', 'user_id', 'id');
  }

  public function groups()
  {
      return $this->hasMany('App\Models\Group', 'created_by_user_id', 'id');
  }
}