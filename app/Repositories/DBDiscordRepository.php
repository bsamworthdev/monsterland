<?php

namespace app\Repositories;

use App\Models\AuditAction;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DBDiscordRepository{

  public function sendNewMonsterWebHook($monster){
    $payload = [
        'username' => "New Monster bot",
        'content' =>  "[".$monster->name."](https://monsterland.net/gallery/".$monster->id.") has just been born!",
        'embeds' =>  [
            [
                'image' => [
                    'author' => '',
                    'title' => '',
                    'description' => '',
                    'url'  =>  "https://monsterland.net/storage/".$monster->id.".png"
                ]
            ]
        ],
    ];
    return $this->sendWebHook($payload);
  }

  public function sendWebHook($payload){
    $url = 'https://discord.com/api/webhooks/828349688247484476/yh_yD6f9efWiYQ8fbBHc3vfPTtow5zPQrohSdJ6xwmOdLvHUyPZlNGF3GwBcZi6Jmp_1';
    return Http::post($url, $payload);
  }
}