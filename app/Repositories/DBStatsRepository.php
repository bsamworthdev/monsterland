<?php

namespace app\Repositories;

use App\Models\MonsterSegment;
use App\Models\Monster;
use App\Models\Rating;
use App\Models\TagScore;
use Illuminate\support\Facades\DB;

class DBStatsRepository{

  function getLeaderBoardStats($masterTaggers = []){

    $ratingStats = $this->getRatingStats();
    $monsterStats = $this->getMonsterStats();
    $taggingStats = $this->getTaggingStats($masterTaggers);

    $stats['ratings_week'] = $ratingStats;
    $stats['monsters_week'] = $monsterStats;
    $stats['tagging_week'] = $taggingStats;
    

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

  function getTaggingStats($masterTaggers = []){
    return TagScore::with(['user'])
      ->where('created_at', '>', DB::raw('date_sub(now(),interval 7 day)'))
      ->whereNotNull('user_id')
      ->whereNotIn('user_id',$masterTaggers)
      ->groupBy('user_id')
      ->orderBy('tag_count','desc')
      ->limit(5)
      ->select('user_id', DB::raw("SUM(score) as tag_count"))
      ->get();
  }

  function getOverallStats(){
    $resp['monster_count'] = $this->getMonsterCount();
    $resp['tagged_percent'] = $this->getTaggedPercent();
    $resp['fully_tagged_percent'] = $this->getFullyTaggedPercent();
    return json_encode($resp);
  }
  
  function getMonsterCount(){
    return Monster::where('status','complete')
      ->count();
  }

  function getTaggedPercent(){
    /* monsters with tags */

    $taggedMonstersCount = Monster::without(['segments'])
      ->withCount([
        'ratings as average_rating' => function($q) {
            $q->select(DB::raw('coalesce(avg(rating),0)'));
        }, 
        'ratings as ratings_count'])
      ->leftJoin('tags', function($join)
      {
          $join->on('monsters.id', 'tags.monster_id');
      })
      ->where('status', 'complete')
      ->whereNotNull('tags.name')
      ->where('nsfl', '0')
      ->where('group_id', '0')
      ->groupBy('monsters.id')
      ->having('average_rating', '>', 6)
      ->having('ratings_count', '>', 2)
      ->get()
      ->count();

    $allMonstersCount = Monster::without(['segments'])
      ->withCount([
        'ratings as average_rating' => function($q) {
            $q->select(DB::raw('coalesce(avg(rating),0)'));
        }, 
        'ratings as ratings_count'])
      ->where('status', 'complete')
      ->where('nsfl', '0')
      ->where('group_id', '0')
      ->having('average_rating', '>', 6)
      ->having('ratings_count', '>', 2)
      ->get()
      ->count();

    $percent = ($taggedMonstersCount/$allMonstersCount) * 100;
    $percent = number_format($percent, 1, '.', '');

    return $percent;
  }

  function getFullyTaggedPercent(){

    $taggedMonstersCount = Monster::without(['segments', 'tagSkips'])
      ->withCount([
        'ratings as average_rating' => function($q) {
            $q->select(DB::raw('coalesce(avg(rating),0)'));
        }, 
        'ratings as ratings_count'])
      ->withCount('tagSkips as tag_skips_count')
      ->withCount('tags as tags_count')
      ->leftJoin('tags', function($join)
      {
          $join->on('monsters.id', 'tags.monster_id');
      })
      ->where('status', 'complete')
      ->whereNotNull('tags.name')
      ->where('nsfl', '0')
      ->where('group_id', '0')
      ->groupBy('monsters.id')
      ->having('average_rating', '>', 6)
      ->having('ratings_count', '>', 2)
      ->having(function($q){
        $q->having('tags_count', '>=', 5)
        ->orHaving('tag_skips_count', '>=', 3);
      })
      ->get()
      ->count();

    $allMonstersCount = Monster::without(['segments'])
      ->withCount([
        'ratings as average_rating' => function($q) {
            $q->select(DB::raw('coalesce(avg(rating),0)'));
        }, 
        'ratings as ratings_count'])
      ->where('status', 'complete')
      ->where('nsfl', '0')
      ->where('group_id', '0')
      ->having('average_rating', '>', 6)
      ->having('ratings_count', '>', 2)
      ->get()
      ->count();

    $percent = ($taggedMonstersCount/$allMonstersCount) * 100;
    $percent = number_format($percent, 1, '.', '');

    return $percent;
  }

}