<?php

namespace app\Repositories;

use App\Models\MonsterSegment;
use App\Models\Rating;
use Illuminate\support\Facades\DB;

class DBStatsRepository{

  function getLeaderBoardStats(){
    $ratingStats = $this->getRatingStats();
    $monsterStats = $this->getMonsterStats();

    $stats['ratings_week'] = $ratingStats;
    $stats['monsters_week'] = $monsterStats;

    return collect($stats);
    // "select user_id, count(*) as ratings_count 
    // from ratings
    // where created_at > date_sub(now(),interval 7 day)
    // group by user_id
    // order by ratings_count desc
    // limit 5"

  }

  function getRatingStats(){
    return Rating::with(['user'])
      ->where('created_at', '>', DB::raw('date_sub(now(),interval 7 day)'))
      ->groupBy('user_id')
      ->orderBy('ratings_count','desc')
      ->limit(5)
      ->select('user_id', DB::raw('count(*) as ratings_count'))
      ->get(); 
  }

  function getMonsterStats(){
    return MonsterSegment::with(['creator'])
      ->where('created_at', '>', DB::raw('date_sub(now(),interval 7 day)'))
      ->where('created_by', '<>', '0')
      ->groupBy('created_by')
      ->orderBy('monster_count','desc')
      ->limit(5)
      ->select('created_by', DB::raw('count(*) as monster_count'))
      ->get();

    // select ms.created_by, count(*) as monster_segments_count
    // from monster_segments ms 
    // inner join monsters m on m.id=ms.monster_id
    // where ms.created_by >0
    // and ms.created_at > date_sub(now(),interval 7 day)
    // and m.status <> 'cancelled'
    // group by ms.created_by
    // order by monster_segments_count desc
    // limit 5

  }
  
}