<?php

namespace app\Repositories;

use App\Models\Monster;
use App\Models\MonsterSegment;
use App\Models\RollbackSuggestion;
use App\Models\TakeTwoRequest;
use App\Models\Rating;
use App\Models\Favourite;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Image;

class DBMonsterRepository{

  function find($id, $with = NULL){
    if ($with) {
      return Monster::with($with)->find($id);
    } else {
      return Monster::find($id);
    }
  }

  function getInstance(){
    return new Monster;
  }

  function getLatestCompletedMonster($user, $group_id){
    return Monster::without(['segments', 'ratings'])
      ->where('nsfl', '0')
      ->where('status','complete')
      ->where('suggest_rollback', '0')
      ->when(!$user || $user->allow_nsfw == 0, function($q) {
          $q->where('nsfw', '0');
      })
      ->where('group_id',$group_id)
      ->orderBy('completed_at', 'desc')
      ->get(['id'])
      ->first();
  }

  function getMonsterById($id, $user, $group_id){

    if ($user && in_array($user->id, [1,2])){
      $monster = Monster::with(['segmentsWithImages','favouritedByUsers'])
        ->where('id',$id)
        ->get()
        ->first();
    } else {
      $monster = Monster::with('favouritedByUsers')
        ->where('id',$id)
        ->when(!$user, function($q) {
            $q->where('status','complete');
        })
        ->where('group_id',$group_id)
        ->when($user && in_array($user->id, [1,2]), function($q) {
          $q->where('suggest_rollback', '0');
        })
        ->get()
        ->first();
    }
    return $monster;
  }

  function getPrevMonster($monster, $user, $group_id){
    return Monster::without(['segments', 'ratings'])
      ->where('id','<>', $monster->id)
      ->when($monster->completed_at, function($q) use($monster) {
          $q->where('completed_at','>', $monster->completed_at);
      })
      ->where('nsfl', '0')
      ->where('suggest_rollback', '0')
      ->when(!$user || $user->allow_nsfw == 0, function($q) {
          $q->where('nsfw', '0');
      })
      ->where('status','complete')
      ->where('group_id', $group_id)
      ->orderBy('completed_at')
      ->get(['id','name'])
      ->first();
  }

  function getNextMonster($monster, $user, $group_id){
    return Monster::without(['segments', 'ratings'])
      ->where('id','<>', $monster->id)
      ->when($monster->completed_at, function($q) use($monster) {
          $q->where('completed_at','<', $monster->completed_at);
      })
      ->where('nsfl', '0')
      ->where('suggest_rollback', '0')
      ->when(!$user || $user->allow_nsfw == 0, function($q) {
          $q->where('nsfw', '0');
      })
      ->where('status','complete')
      ->where('group_id', $group_id)
      ->orderBy('completed_at', 'desc')
      ->get(['id','name'])
      ->first();

  }

  function rollbackMonster($monster_id, $segments_name, $segments){

    //Do rollback
    $monster = $this->find($monster_id, 'segments'); 
    if ($segments_name == 'legs'){
        $monster->status = 'awaiting legs';
        $monster->direction = 'down';
    }
    elseif ($segments_name == 'body_legs'){
      $monster->status = 'awaiting body';
      $monster->direction = 'down';
    }
    elseif ($segments_name == 'head'){
      $monster->status = 'awaiting head';
      $monster->direction = 'up';
    }
    elseif ($segments_name == 'head_body'){
      $monster->status = 'awaiting body';
      $monster->direction = 'up';
    }

    $monster->suggest_rollback = 0;
    $monster->image = NULL;
    $monster->save();

    //Roll back ratings
    Rating::where('monster_id',$monster_id)
      ->delete();

    //Set suggestion as accepted
    RollbackSuggestion::where('monster_id',$monster_id)
      ->update([
        'status' => 'accepted'
      ]);

    //Remove from user_linked_monsters table
    foreach($monster->segments as $monster_segment){
      if (in_array($monster_segment->segment,$segments) && $monster_segment->created_by > 0) {
        DB::table('user_linked_monsters')
          ->where('monster_id',$monster_id)
          ->where('user_id',$monster_segment->created_by)
          ->delete();
      }
    }
    
  }

  function flagMonster($monster_id, $severity){

    $monster = $this->find($monster_id);

    if ($severity == 'nsfl'){
      $monster->nsfl = 1;
      $monster->nsfw = 1;
      $rollback_status = 'censored';
    } else if ($severity == 'nsfw'){
      $monster->nsfl = 0;
      $monster->nsfw = 1;
      $rollback_status = 'censored';
    } else if ($severity == 'safe'){
      $monster->nsfl = 0;
      $monster->nsfw = 0;
      $monster->approved_by_admin = 1;
      $rollback_status = 'rejected';
    } else if ($severity == 'validated'){
      //validated that latest 
    }
    $monster->suggest_rollback = 0;

    $monster->save();

    RollbackSuggestion::where('monster_id',$monster_id)
      ->update([
        'status' => $rollback_status
      ]);
  }

  function validateMonster($monster_id){
    $monster = $this->find($monster_id);
    $monster->needs_validating = 0;
    $monster->save();
  }

  function abortMonster($id){
    $monster = $this->find($id);
    $monster->status = 'cancelled';
    $monster->suggest_rollback = 0;
    $monster->save();
  }

  function resetUserMonsters($monster_id, $session_id){
    Monster::where('in_progress','1')
      ->where('id','<>', $monster_id)
      ->where('in_progress_with_session_id', $session_id)
      ->update(
          [
          'in_progress' => 0, 
          'in_progress_with' => 0, 
          'in_progress_with_session_id' => NULL
          ]
      );

    RollbackSuggestion::where('monster_id',$monster_id)
      ->update([
        'status' => 'accepted'
      ]);
  }

  function takeTwoOnMonster($monster_id, $segment_name){
    //Monster
    $existing_monster = Monster::find($monster_id);
    
    $monster_name = $existing_monster->name;
    $new_monster = $existing_monster->replicate();
    $pattern = "/(\(v.*?)\)/"; //e.g. (v2)

    
    if (preg_match($pattern, $monster_name) > 0){
      $start_pos = strpos($monster_name, '(v');
      $end_pos = strpos($monster_name, ')',$start_pos);
      $version = substr($monster_name, ($start_pos+2), ($end_pos-$start_pos-2));
      $new_monster_name = substr($existing_monster->name, 0, $start_pos)." (v".($version+1).")";
    } else {
      $new_monster_name = $existing_monster->name." (v2)";
    }
    $new_monster->name = $new_monster_name;
    $new_monster->image = NULL;
    $new_monster->request_take_two = 0;
    if ($segment_name == 'head'){
      $new_monster->status = "awaiting body";
      $new_monster->direction = "down";
    } elseif ($segment_name == 'head_body'){
      $new_monster->status = "awaiting legs";
      $new_monster->direction = "down";
    } elseif ($segment_name == 'legs'){
      $new_monster->status = "awaiting body";
      $new_monster->direction = "up";
    } elseif ($segment_name == 'legs_body') {
      $new_monster->status = "awaiting head";
      $new_monster->direction = "up";
    }
    $new_monster->save();
    $new_monster_id = $new_monster->id;

    //Segments
    $existing_segments = MonsterSegment::where('monster_id', $monster_id)
      ->when($segment_name == 'head', function($q){
        $q->where('segment','head');
      })
      ->when($segment_name == 'head_body', function($q){
        $q->whereIn('segment',['head','body']);
      })
      ->when($segment_name == 'legs', function($q){
        $q->whereIn('segment',['legs']);
      })
      ->when($segment_name == 'legs_body', function($q){
        $q->whereIn('segment',['legs','body']);
      })
      ->get();

    foreach ($existing_segments as $existing_segment){
      $new_segment = $existing_segment->replicate();
      $new_segment->monster_id = $new_monster_id;
    
      if ($new_segment->image_path === 'NULL' || $new_segment->image_path == ''){
        //$new_segment->image = 'data:image/png;base64,'.$this->base64EncodeSegment($new_segment->segment, $monster_image);
        $monster_image_path = $existing_monster->image;
        $monster_image = Storage::disk('public')->get(basename($monster_image_path));
        $new_segment->image_path = $new_segment->createSegmentImageFromFullImage($monster_image, $existing_monster->direction);
      } else {
        $new_segment->image_path = $new_segment->cloneSegmentImage($existing_segment->monster_id);
      }
      $new_segment->save();
    }

    TakeTwoRequest::where('monster_id', $monster_id)->update([
      'status' => 'accepted'
    ]);
  }

  // function base64EncodeSegment($segment, $image){

  //   if ($segment == 'head'){
  //     $cropped_image = Image::make($image)->crop(800, 266, 0, 0)->encode('png');
  //   } elseif($segment == 'body') {
  //     $cropped_image = Image::make($image)->crop(800, 299, 0, 236)->encode('png');
  //   }
  //   return base64_encode($cropped_image); 
  // }

  function rejectTakeTwoOnMonster($monster_id){
    TakeTwoRequest::where('monster_id', $monster_id)->update([
      'status' => 'rejected'
    ]);
  }

  function startMonster($id, $user_id, $session_id){
    $monster = $this->find($id);
    $monster->in_progress = 1;
    $monster->in_progress_with = $user_id;
    $monster->in_progress_with_session_id = $session_id;
    $monster->save();
  }

  function cancelMonster($id){
    $monster = $this->find($id);
    $monster->in_progress = 0;
    $monster->in_progress_with = 0;
    $monster->in_progress_with_session_id = NULL;
    $monster->save();
  }
  
  function reviveImage($id, $segment_name, $user_id, $session_id){

    $monster = $this->find($id);

    if ($monster->in_progress || $monster->status != 'awaiting '.$segment_name) {
      //too late
      return 'unrevived';
    } else {
      //revive
      $monster = $this->find($id);
      $monster->in_progress = 1;
      $monster->in_progress_with = $user_id;
      $monster->in_progress_with_session_id = $session_id;
      $monster->save();
      return 'revived';
    }
  }

  function cancelInactiveMonsters(){
    //Cancel monster if inactive for 30 minutes
    Monster::where('in_progress','1')
      ->where('updated_at', '<', 
          Carbon::now()->subMinutes(30)->toDateTimeString()
      )
      ->update(
          [
          'in_progress' => 0, 
          'in_progress_with' => 0, 
          'in_progress_with_session_id' => NULL
          ]
      );
  }

  function createMissingMonsterImages(){
    $monsters = Monster::where('status','complete')
      ->whereNull('image')
      ->get();
    foreach($monsters as $monster){
        $monster = $this->find($monster->id); 
        $image = $monster->createImage();
        $thumbnail_image = $monster->createThumbnailImage();
        $monster->image = $image;
        $monster->thumbnail_image = $thumbnail_image;
        $monster->save();
    } 
  }

  function createMissingThumbnailImages(){
    $monsters = Monster::where('status','complete')
      ->whereNull('thumbnail_image')
      ->whereNotNull('image')
      ->orderBy('completed_at','DESC')
      ->limit(100)
      ->get();
    foreach($monsters as $monster){
        $monster = $this->find($monster->id); 
        $thumbnail_image = $monster->createThumbnailImage();
        $monster->thumbnail_image = $thumbnail_image;
        $monster->save();
    } 
  }

  function removeOldB64Images(){
    //Find ids of monsters created more than 3 days ago
    $monster_ids = Monster::where('completed_at','<',DB::raw('date_sub(now(),interval 3 day)'))
      ->whereIn('status',['complete','cancelled'])
      ->whereNotNull('image') 
      ->where('image','<>','n/a') 
      ->where('image','<>','') 
      ->pluck('id');

    //Clear base64 images older than 10 days
    MonsterSegment::whereIn('monster_id',$monster_ids)
      ->update(
          [
          'image' => ''
          ]
      );
  }

  function getTopMonsters($user, $group_id, $date = NULL, $search = '', $page = -1){
    return Monster::withCount([
      'ratings as average_rating' => function($q) {
          $q->select(DB::raw('coalesce(avg(rating),0)'));
      }, 
      'ratings as ratings_count'])
      ->where('status', 'complete')
      ->where('suggest_rollback', '0')
      ->when($date, function($q) use ($date) {
        $q->where('completed_at','>=',$date);
      })
      ->where('nsfl', '0')
      ->when(!$user || $user->allow_nsfw == 0, function($q) {
          $q->where('nsfw', '0');
      })
      ->where('group_id', $group_id)
      ->where('name','LIKE','%'.$search.'%')
      ->when($group_id == 0, function($q) {
          $q->having('average_rating', '>', 0)
          ->having('ratings_count', '>', 0);
      })
      ->orderBy('average_rating','desc')
      ->orderBy('ratings_count', 'desc')
      ->orderBy('name', 'asc')
      ->when($page <> -1, function($q) use ($page) {
        $q->skip($page*8)
          ->take(8);
      })
      ->get();
  }

  function getMonsters($user, $group_id = 0, $search = '', 
    $favourites_only = false, $followed_only = false, $nsfw_only = false,
    $unrated_only = false, $my_monsters_only = false, $user_monsters_only = 0, $sort_by = 'latest', 
    $date = '', $skip = 0, $all = false){
    

    $tagSearch = false;
    if ($search && $search[0] == '#'){
      $tagSearch = true;
      $search = substr($search, 1);
    }

    $result = Monster::withCount([
      'ratings as average_rating' => function($q) {
          $q->select(DB::raw('coalesce(avg(rating),0)'));
      }, 
      'ratings as ratings_count'])
      ->where('status', 'complete')
      ->where('suggest_rollback', '0')
      ->when($date, function($q) use ($date) {
        $q->where('completed_at','>=',$date);
      })
      ->where('nsfl', '0')
      ->when(!$user || $user->allow_nsfw == 0, function($q) {
          $q->where('nsfw', '0');
      })
      ->where('group_id', $group_id)
      ->when($tagSearch, function($q) use ($search){
        return $q->join('tags', function ($join) use ($search) {
          $join->on('tags.monster_id', '=', 'monsters.id')
            ->where('tags.name', $search);
        });
      })
      ->when(!$tagSearch, function($q) use ($search){
        return $q->where('name','LIKE','%'.$search.'%');
      })
      ->when($user && $favourites_only, function($q) use ($user) {
        return $q->join('favourites', function ($join) use ($user) {
          $join->on('favourites.monster_id', '=', 'monsters.id')
            ->where('favourites.user_id', $user->id);
        });
      })
      ->when($user && $followed_only, function($q) use ($user) {
        return $q->join('monster_segments as ms1', function ($join) use ($user) {
            $join->on('ms1.monster_id', '=', 'monsters.id')
              ->join('follows', function ($join2) {
                return $join2->on('follows.followed_user_id','=','ms1.created_by');
              })
            ->where('follows.follower_user_id', $user->id);
        });
      })
      ->when($user && $nsfw_only, function($q) {
        $q->where('nsfw', '1');
      })
      ->when($user && $my_monsters_only, function($q) use ($user) {
        return $q->join('monster_segments as ms2', function ($join) use ($user) {
            $join->on('ms2.monster_id', '=', 'monsters.id')
            ->on('ms2.created_by', '=', DB::raw($user->id));
        });
      })
      ->when($user_monsters_only > 0, function($q) use ($user_monsters_only) {
        return $q->join('monster_segments as ms3', function ($join) use ($user_monsters_only) {
            $join->on('ms3.monster_id', '=', 'monsters.id')
            ->on('ms3.created_by', '=', DB::raw($user_monsters_only));
        });
      })
      ->when($user && $unrated_only, function($q) use ($user) {
        return $q->leftJoin('ratings', function ($join) use ($user) {
            $join->on('ratings.monster_id', '=', 'monsters.id')
              ->on('ratings.user_id', '=', DB::raw($user->id));
        })
        ->whereNull('ratings.user_id')
        ->leftJoin('monster_segments as ms4', function ($join) use ($user) {
            $join->on('ms4.monster_id', '=', 'monsters.id')
              ->on('ms4.created_by', '=', DB::raw($user->id));
        })
        ->whereNull('ms4.created_by');
      })
      ->distinct()
      ->when($sort_by == 'highest_rated', function($q) {
        return $q->orderBy('average_rating','desc')
          ->orderBy('ratings_count', 'desc')
          ->orderBy('name', 'desc');
      })
      ->when($sort_by == 'lowest_rated', function($q) {
        return $q->having('average_rating', '>', 0)
          ->having('ratings_count', '>', 0)
          ->orderBy('average_rating','asc')
          ->orderBy('ratings_count', 'asc')
          ->orderBy('name', 'asc');
      })
      ->when($sort_by == 'newest', function($q) {
        return $q->orderBy('completed_at','desc');
      })
      ->when($sort_by == 'oldest', function($q) {
        return $q->orderBy('completed_at','asc');
      })
      ->when($skip, function($q) use ($skip) {
        $q->skip($skip);
      })
      ->when($all, function($q) use ($skip) {
        $q->take(80);
      })
      ->when(!$all, function($q) use ($skip) {
        $q->take(8);
      })
      ->get();

      if ($all){
        $result = $result->pluck('id')->toArray();
      }

      return $result;
  }

  // function getMonstersIds($user, $group_id = 0, $search = '', 
  //   $favourites_only = false, $followed_only = false, $nsfw_only = false,
  //   $unrated_only = false, $my_monsters_only = false, $user_monsters_only = 0, $sort_by = 'latest', 
  //   $date = '', $skip = 0){
  //     //Identical to getMonsters() except it runs the SQL directly without fetching models, so it's a bit quicker
    
  //   $result = DB::table('monsters')
  //     ->select(DB::Raw('DISTINCT `monsters`.id,`monsters`.name, `monsters`.completed_at,
  //         (
  //           SELECT Coalesce(Avg(rating), 0)
  //           FROM   `ratings`
  //           WHERE  `monsters`.`id` = `ratings`.`monster_id`
  //         ) AS `average_rating`,
  //         (
  //           SELECT Count(*)
  //           FROM   `ratings`
  //           WHERE  `monsters`.`id` = `ratings`.`monster_id`
  //         ) AS`ratings_count`'
  //       )) 
  //       ->where('status', 'complete')
  //       ->where('suggest_rollback', '0')
  //       ->when($date, function($q) use ($date) {
  //         $q->where('completed_at','>=',$date);
  //       })
  //       ->where('nsfl', '0')
  //       ->when(!$user || $user->allow_nsfw == 0, function($q) {
  //           $q->where('nsfw', '0');
  //       })
  //       ->where('group_id', $group_id)
  //       ->where('name','LIKE','%'.$search.'%')
  //       ->when($user && $favourites_only, function($q) use ($user) {
  //         return $q->join('favourites', function ($join) use ($user) {
  //           $join->on('favourites.monster_id', '=', 'monsters.id')
  //             ->where('favourites.user_id', $user->id);
  //         });
  //       })
  //       ->when($user && $followed_only, function($q) use ($user) {
  //         return $q->join('monster_segments as ms1', function ($join) use ($user) {
  //             $join->on('ms1.monster_id', '=', 'monsters.id')
  //               ->join('follows', function ($join2) {
  //                 return $join2->on('follows.followed_user_id','=','ms1.created_by');
  //               })
  //             ->where('follows.follower_user_id', $user->id);
  //         });
  //       })
  //       ->when($user && $nsfw_only, function($q) {
  //         $q->where('nsfw', '1');
  //       })
  //       ->when($user && $my_monsters_only, function($q) use ($user) {
  //         return $q->join('monster_segments as ms2', function ($join) use ($user) {
  //             $join->on('ms2.monster_id', '=', 'monsters.id')
  //             ->on('ms2.created_by', '=', DB::raw($user->id));
  //         });
  //       })
  //       ->when($user_monsters_only > 0, function($q) use ($user_monsters_only) {
  //         return $q->join('monster_segments as ms3', function ($join) use ($user_monsters_only) {
  //             $join->on('ms3.monster_id', '=', 'monsters.id')
  //             ->on('ms3.created_by', '=', DB::raw($user_monsters_only));
  //         });
  //       })
  //       ->when($user && $unrated_only, function($q) use ($user) {
  //         return $q->leftJoin('ratings', function ($join) use ($user) {
  //             $join->on('ratings.monster_id', '=', 'monsters.id')
  //               ->on('ratings.user_id', '=', DB::raw($user->id));
  //         })
  //         ->whereNull('ratings.user_id')
  //         ->leftJoin('monster_segments as ms4', function ($join) use ($user) {
  //             $join->on('ms4.monster_id', '=', 'monsters.id')
  //               ->on('ms4.created_by', '=', DB::raw($user->id));
  //         })
  //         ->whereNull('ms4.created_by');
  //       })
  //       ->distinct()
  //       ->when($sort_by == 'highest_rated', function($q) {
  //         return $q->orderBy('average_rating','desc')
  //           ->orderBy('ratings_count', 'desc')
  //           ->orderBy('name', 'desc');
  //       })
  //       ->when($sort_by == 'lowest_rated', function($q) {
  //         return $q->having('average_rating', '>', 0)
  //           ->having('ratings_count', '>', 0)
  //           ->orderBy('average_rating','asc')
  //           ->orderBy('ratings_count', 'asc')
  //           ->orderBy('name', 'asc');
  //       })
  //       ->when($sort_by == 'newest', function($q) {
  //         return $q->orderBy('completed_at','desc');
  //       })
  //       ->when($sort_by == 'oldest', function($q) {
  //         return $q->orderBy('completed_at','asc');
  //       })
  //       ->when($skip, function($q) use ($skip) {
  //         $q->skip($skip);
  //       })
  //       ->take(80)
  //       ->get()
  //       ->pluck('id')->toArray();
  

  //     // Log::Debug($result->toSql());
  //     // Log::Debug($result->getBindings());

  //     return $result;
  // }

  function getTopMonstersByUser($selected_user, $current_user, $date, $search, $page){

    return Monster::withCount([
      'ratings as average_rating' => function($query) {
          $query->select(DB::raw('coalesce(avg(rating),0)'));
      }, 
      'ratings as ratings_count'])
      ->join('monster_segments', 'monster_segments.monster_id', '=', 'monsters.id')
      ->where('monsters.status', 'complete')
      ->where('monsters.completed_at','>=',$date)
      ->where('monster_segments.created_by',$selected_user->id)
      ->where('suggest_rollback', '0')
      ->where('group_id','0')
      ->where('nsfl', '0')
      ->when(!$current_user || $current_user->allow_nsfw == 0, function($q) {
          $q->where('nsfw', '0');
      })
      ->where('name','LIKE','%'.$search.'%')
      ->groupBy('monsters.id')
      ->orderBy('average_rating','desc')
      ->orderBy('ratings_count', 'desc')
      ->orderBy('monsters.name', 'asc')
      ->skip($page*8)
      ->take(8)
      ->get();
      
  }

  function getFavouritesByUser($selected_user, $current_user, $date, $search, $page){
    return Monster::withCount([
      'ratings as average_rating' => function($query) {
          $query->select(DB::raw('coalesce(avg(rating),0)'));
      }, 
      'ratings as ratings_count'])
      ->join('favourites', function ($join) use ($selected_user) {
        $join->on('favourites.monster_id', '=', 'monsters.id')
          ->where('favourites.user_id', $selected_user->id);
      })
      ->where('monsters.status', 'complete')
      ->where('monsters.completed_at','>=',$date)
      ->where('suggest_rollback', '0')
      ->where('nsfl', '0')
      ->when(!$current_user || $current_user->allow_nsfw == 0, function($q) {
          $q->where('nsfw', '0');
      })
      ->where('name','LIKE','%'.$search.'%')
      ->groupBy('monsters.id')
      ->orderBy('average_rating','desc')
      ->orderBy('ratings_count', 'desc')
      ->orderBy('monsters.name', 'asc')
      ->skip($page*8)
      ->take(8)
      ->get();
  }

  function getUnfinishedMonsters($user = NULL, $group_id = 0){
    $monsters = Monster::where('status', '<>', 'complete')
      ->where('status', '<>', 'cancelled')
      ->where(function($q){
        $q->where(function($q1){
          $q1->where('status', '<>', 'awaiting head')
            ->where('direction','down');
        })
        ->orWhere(function ($q2){
            $q2->where('status', '<>', 'awaiting legs')
              ->where('direction','up');
        });
      })
      ->where('nsfl', '0')
      ->where('suggest_rollback', '0')
      ->when(!$user || $user->allow_nsfw == 0, function($q) {
        $q->where('nsfw', '0');
      })
      ->where('group_id', $group_id)
      ->orderBy('created_at', 'desc')
      ->setEagerLoads(['segments' => function ($q) {
        $q->get();
      }])
      ->get(['id', 'name', 'in_progress', 'nsfw','nsfl','group_id','vip','needs_validating','direction','status','auth','created_at',
          DB::Raw("(updated_at<'".Carbon::now()->subMinutes(10)->toDateTimeString()."') as abandoned") 
      ]);
      $monsters->append('created_at_tidy');
      
      return $monsters;
  }

  function getFlaggedMonsters(){
    return Monster::where('suggest_rollback', '1')
      ->where('group_id', '0')
      ->get(['id', 'name', 'nsfw','status']);
  }

  function getFlaggedCommentMonsters(){
    return Monster::join('comments', 'monsters.id','=','comments.monster_id')
      ->where('comments.spam', '1')
      ->where('comments.deleted', '0')
      ->distinct()
      ->get(['monsters.id', 'monsters.name', 'monsters.nsfw','monsters.status']);
  }

  function getMonitoredMonsters(){
    return Monster::where('needs_validating', '1')
      ->get(['id', 'name', 'nsfw','status']);
  }

  function getTakeTwoMonsters(){
    return Monster::join('take_two_requests', 'monsters.id','=','take_two_requests.monster_id')
      ->where('take_two_requests.status', 'pending')
      ->get(['monsters.id', 'monsters.name', 'monsters.nsfw','monsters.status']);
  }

  function suggestMonsterRollback($user_id, $monster_id){
    Monster::where('id', $monster_id)
      ->update(
          [
          'suggest_rollback' => 1
          ]
      );
    RollbackSuggestion::create([
      'monster_id' => $monster_id,
      'requested_by' => $user_id,
      'status' => 'pending'
    ]);
  }

  function requestTakeTwoOnMonster($user_id, $monster_id, $segment_name){
    Monster::where('id', $monster_id)
    ->update(
        [
        'request_take_two' => 1
        ]
    );
    TakeTwoRequest::create([
      'monster_id' => $monster_id,
      'requested_by' => $user_id,
      'from_segment' => $segment_name,
      'status' => 'pending'
    ]);
  }

  function isAuth($level, $authUser){
    $auth=0;
    switch ($level){
        case 'basic':
            $auth = 0;
            break;
        case 'standard':
            if (!$authUser) return 0;
            $auth = 1;
            break;
        case 'pro':
            if (!$authUser) return 0;
            $auth = 1;
            break;
    }
    return $auth;
  }

  function isVIP($level, $vipUser){
    $isVip=0;
    switch ($level){
        case 'basic':
          $isVip = 0;
            break;
        case 'standard':
          $isVip = 0;
            break;
        case 'pro':
            if (!$vipUser) return 0;
            $isVip = 1;
            break;
    }
    return $isVip;
  }

  function updateMonsterName($user_id, $monster_id, $monster_name){

    Monster::where('id', $monster_id)
      ->where('in_progress_with', $user_id)
      ->update(
          [
          'name' => $monster_name
          ]
      );
  }

  function updateMonsterNSFW($user_id, $monster_id, $is_nsfw){

    Monster::where('id', $monster_id)
      ->where('in_progress_with', $user_id)
      ->update(
          [
          'nsfw' => $is_nsfw
          ]
      );
  }
  
  function updateMonsterLevel($user_id, $monster_id, $monster_level){

    switch ($monster_level){
      case 'basic':
        $isVip = 0;
        $isAuth = 0;
          break;
      case 'standard':
        $isVip = 0;
        $isAuth = 1;
          break;
      case 'pro':
          $isVip = 1;
          $isAuth = 1;
          break;
    }

    Monster::where('id', $monster_id)
      ->where('in_progress_with', $user_id)
      ->update(
          [
            'vip' => $isVip,
            'auth' => $isAuth
          ]
      );
  }

  function getFeaturedMonsters(){
    return Monster::where('featured', '1')
      ->get();
  }

  function getRandomMonster(){
    return Monster::without(['segments','ratings'])
      ->select('id','name','created_at')
      ->where('created_at', '<',  DB::raw('date_sub(now(),interval 4 week)'))
      ->where('nsfw',0)
      ->where('nsfl',0)
      ->where('status','complete')
      ->withCount([
        'ratings as average_rating' => function($query) {
            $query->select(DB::raw('coalesce(avg(rating),0)'));
        }, 
        'ratings as ratings_count'])
      ->having('average_rating', '>', 8)
      ->having('ratings_count', '>', 1)
      ->inRandomOrder()
      ->get()
      ->first();
  }

  function getMonstersToTag($user, $hasSubmissionOnly = true){
    $result = Monster::with(['tags','tagSubmissions', 'tagSkips'])
      ->withCount([
      'ratings as average_rating' => function($q) {
          $q->select(DB::raw('coalesce(avg(rating),0)'));
      }, 
      'ratings as ratings_count'])
      ->withCount('tagSubmissions as tag_submissions_count')
      ->withCount('tagSkips as tag_skips_count')
      ->withCount(['tags as tags_count' => function ($q) {
        $q->whereNull('manually_added_by');
      }])
      ->where('status', 'complete')
      ->where('suggest_rollback', '0')
      ->where('nsfl', '0')
      ->when(!$user || $user->allow_nsfw == 0, function($q) {
          $q->where('nsfw', '0' );
      })
      ->where('group_id', '0')
      ->distinct()
      ->having('average_rating', '>', 6)
      ->having('ratings_count', '>', 1)
      ->when($hasSubmissionOnly, function($q){
        $q->havingRaw('tag_submissions_count > (2 * tags_count)');
      })
      ->when(!$hasSubmissionOnly, function($q){
        $q->havingRaw('tag_submissions_count = 0');
      })
      ->having('tags_count','<',5)
      ->having('tag_skips_count', '<', 3)
      ->inRandomOrder()
      ->take(300)
      ->get();

      return $result;
  
  }

  function updateAuthLevel($monster_id, $level){
    switch ($level){
      case 'basic':
        $isVip = 0;
        $isAuth = 0;
          break;
      case 'standard':
        $isVip = 0;
        $isAuth = 1;
          break;
      case 'pro':
          $isVip = 1;
          $isAuth = 1;
          break;
    }
    Monster::where('id', $monster_id)
      ->where('in_progress',0)
      ->update(
          [
          'vip' => $isVip,
          'auth' => $isAuth
          ]
      );
  }

  function addFavourite($user_id, $monster_id){
    Favourite::updateOrInsert([
        'user_id' => $user_id, 
        'monster_id' => $monster_id,
      ],[
        'user_id' => $user_id, 
        'monster_id' => $monster_id,
        'created_at' => now(),
        'updated_at' => now()
      ]
    );
  }

  function removeFavourite($user_id, $monster_id){
    Favourite::where('user_id', $user_id)
      ->where('monster_id', $monster_id)
      ->delete();
  }    

  function updateLastUpdated($monster_id){
    Monster::where('id', $monster_id)
      ->update(
          [
          'updated_at' => now()
          ]
      );
  }
}