<?php

namespace app\Repositories;

use Illuminate\Support\Facades\DB;
use App\Models\Tag;
use App\Models\TagScore;
use App\Models\TagSubmission;
use App\Models\TagSkip;
use App\Models\Profanity;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DBTagRepository{
  
  function find($tag_id){
    return Tag::find($tag_id);
  }

  function saveSubmission($user_id, $session_id, $monster_id, $name){
    if (Profanity::where('word', $name)->count() > 0) return;

    $tagSubmission = new TagSubmission;
    if ($user_id) $tagSubmission->user_id = $user_id;
    $tagSubmission->session_id = $session_id;
    $tagSubmission->monster_id = $monster_id;
    $tagSubmission->name = $name;
    $tagSubmission->save();
  }

  function getTagSubmissions($monster_id, $name){
    return TagSubmission::where('monster_id',$monster_id)
      ->where('name', $name)
      ->get();
  }

  function saveTag($monster_id, $name){
    $tagSubmission = new Tag;
    $tagSubmission->monster_id = $monster_id;
    $tagSubmission->name = strtolower($name);
    $tagSubmission->save();
  }

  function removeTag($monster_id, $tag_id){
    $tag = Tag::find($tag_id);

    TagSubmission::where('monster_id', $monster_id)
      ->where('name', $tag->name)
      ->delete();

    Tag::where('id', $tag_id)
      ->delete();

  }

  function addTag($user_id, $monster_id, $name){
  
    $tag = new Tag;
    // $tag->user_id = $user_id;
    $tag->monster_id = $monster_id;
    $tag->manually_added_by = $user_id;
    $tag->name = strtolower($name);
    $tag->save();

    return $tag;
  }

  function validateScore($user_id, $score){
    if ($score == 0) return true;

    //Check the score doesn't exceed recent submissions
    $tagSubmissionCount = TagSubmission::where('user_id',$user_id)
      ->where('created_at', '>', Carbon::now()->subHour())
      ->count();
    if ($tagSubmissionCount < $score){
      return false;
    } 

    //Check the timer wasn't stopped
    $tagSubmissions = TagSubmission::where('user_id',$user_id)->orderBy('created_at','desc')->limit($score-1);
    
    $overTimeLimit = false;
    $prevTime = NULL;
    for($i = 0; $i < $score; $i++){
      $time = $tagSubmissions->skip($i)->take(1)->get()->first()->created_at;
      if ($prevTime){
        $diff = (new Carbon($time))->diffInSeconds($prevTime);
        // Log::Debug($time.'     '.$diff);
        if ($diff > 30){
          $overTimeLimit = true;
          break;
        } 
      }
      $prevTime = $time;
    }
    if ($overTimeLimit) return false;

    return true;
  }

  function saveTagScore($user_id, $score){
    $tagSubmission = new TagScore;
    $tagSubmission->user_id = $user_id;
    $tagSubmission->score = $score;
    $tagSubmission->save();
  }

  function getTopScore($when = 'ever', $masterTaggers = [], $user_id = NULL){
    if ($when == 'today');
    return TagScore::join('users', 'users.id', '=', 'tag_scores.user_id')
      ->when($when == 'today', function($q){
        $q->where('tag_scores.created_at', '>', Carbon::now()->subDay());
      })
      ->when($user_id, function($q) use($user_id){
        $q->where('tag_scores.user_id',$user_id);
      })
      ->when(!$user_id, function($q) use($masterTaggers){
        $q->whereNotIn('tag_scores.user_id',$masterTaggers);
      })
      ->orderBy('score','desc')
      ->select('score','users.name as user_name')
      ->first(); 
  }

  function saveSkip($user_id, $monster_id){
    $tagSkip = new TagSkip;
    $tagSkip->user_id = $user_id;
    $tagSkip->monster_id = $monster_id;
    $tagSkip->save();
  }

  function awardTagPrize($user, $score){
    
    $peekCount = $user->peek_count;
    $redrawCount = $user->take_two_count;

    if ($score >= 100){
      $peekCount+=5;
      $redrawCount+=5;
    } elseif ( $score >= 50){
      $peekCount+=2;
      $redrawCount+=2;
    } elseif ( $score >= 30){
      $peekCount+=1;
      $redrawCount+=1;
    } elseif ( $score >= 10){
      //Log::Debug('test2');
      $peekCount+=1;
    }

    $user->peek_count = $peekCount;
    $user->take_two_count = $redrawCount;
  
    $user->update();
  }

  function getRandomTags($user, $count = 5){
      $tags = DB::table('tags')
        ->join('monsters','tags.monster_id','monsters.id')
        ->when(!$user->allow_nsfw, function($q) {
          $q->where('monsters.nsfw',0);
        })
        ->where('monsters.group_id',0)
        ->where('monsters.nsfl',0)
        ->groupBy('tags.name')
        ->having(DB::raw('count(tags.name)'), '>', 5)
        ->inRandomOrder()
        ->take($count)
        ->get(['tags.name', DB::raw('count(tags.name) as tag_count')]);
    return $tags;
  }

}