<?php

namespace app\Repositories;

use App\Models\Setting;
use Illuminate\support\Facades\DB;

class DBSettingsRepository{

  function getValue($name){
    $values = Setting::where('name', $name)->first();
    if (!$values) return '';
    return $values->value;
  }

  function everyoneCanUseStore(){
    return $this->getValue('store_setting') === 'everyone';
  }

  function redisIsActive(){
    return $this->getValue('redis') === 'on';
  }

  function activateRedis(){
    Setting::where('name','redis')
      ->update([
        'value' => 'on'
      ]);
  }

  function deactivateRedis(){
    Setting::where('name','redis')
      ->update([
        'value' => 'off'
      ]);
  }

  function getMasterTaggers(){
    return [$this->getValue('master_taggers')];
  }
}