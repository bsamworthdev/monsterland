<?php

namespace app\Repositories;

use App\Models\InfoMessage;
use App\Models\Monster;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\User;

class DBInfoMessageRepository{

  function getActiveMessages($user_id = 0){

    if ($user_id > 0) {
      $user = User::find($user_id);
    }
    $messages = InfoMessage::where('start_date', '<', DB::raw('now()'))
      ->where('end_date', '>' , DB::raw('now()'))
      ->when($user_id == 0, function($q){
        $q->whereIn('member_status', ['non-members','any']);
      })
      ->when($user_id > 0, function($q) use($user){
        $q->where(function ($q1) use($user){
          $q1->whereIn('member_status', ['members','any'])
            ->when($user->is_patron, function($q2) use($user){
              $q2->orWhere('member_status', 'patrons');
            })
            ->when($user->moderator, function($q2) use($user){
              $q2->orWhere('member_status', 'moderators');
            })
            ->when($user->vip, function($q2) use($user){
              $q2->orWhere('member_status', 'vips');
            });
        });
      })
      ->where(function ($q) use($user_id){
          $q->whereNull('user')
          ->orWhere('user', $user_id);
      })
      ->whereDoesntHave('closed_info_messages', function($q) use($user_id){
          $q->where('user_id', $user_id);
      })
      ->get();

      return $messages;
  }

  function addWeeklyTrophiesMessage($monsterIds){

    $firstPlaceMonster = Monster::find($monsterIds['first']);
    $secondPlaceMonster = Monster::find($monsterIds['second']);
    $thirdPlaceMonster = Monster::find($monsterIds['third']);

    $text=
      "Top rated monsters this week:
      <br>
      GOLD: \"<a href='/gallery/".$firstPlaceMonster->id."'>".
      $firstPlaceMonster->name.
      "</a>".($firstPlaceMonster->nsfw ? ' (NSFW)' : '')."\"
      <br>
      SILVER: \"<a href='/gallery/".$secondPlaceMonster->id."'>".
      $secondPlaceMonster->name.
      "</a>".($secondPlaceMonster->nsfw ? ' (NSFW)' : '')."\"
      <br>
      BRONZE: \"<a href='/gallery/".$thirdPlaceMonster->id."'>".
      $thirdPlaceMonster->name.
      "</a>".($thirdPlaceMonster->nsfw ? ' (NSFW)' : '')."\"
      <br><br>
      Congratulations to everyone involved. Trophies are coming your way.";

    $this->addInfoMessage($text, NULL, NULL, 'success');
  }

  function addInfoMessage($text, $user_id=NULL, $member_status=NULL, $style=NULL, 
    $start_date = NULL, $duration = 1){

    $infoMessage = new InfoMessage;
    $infoMessage->text = $text;
    if ($user_id) $infoMessage->user = $user_id;
    if ($member_status) $infoMessage->member_status = $member_status;
    if ($style) $infoMessage->style = $style;

    if (!$start_date) $start_date = Carbon::now();
    
    $infoMessage->start_date = $start_date;
    $infoMessage->end_date = $start_date->copy()->addDays($duration);
    $infoMessage->save();
    
  }
}