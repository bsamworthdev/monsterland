<?php

namespace app\Services;

use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;
use App\Models\Setting;

class RedisService{
  
  function get($key){
    $active = Setting::where('name','redis')->first();
    if ($active && $active->value== 'on'){
      return Redis::get($key);
    } else {
      return '';
    }
  }

  function set($key, $value){
    $active = Setting::where('name','redis')->first();
    if ($active && $active->value== 'on'){
      return Redis::set($key, $value);
    } else {
      return '';
    }
  }

  function delete($key, $exactMatch = true){
    $active = Setting::where('name','redis')->first();
    if ($active && $active->value== 'on'){
      if ($exactMatch){
        return Redis::del($key);
      } else {
        $keys = (Redis::keys($key.'*'));
        if (count($keys)){
          return Redis::del($keys);
        }
      }
    } else {
      return '';
    }
  }

  function flushDB(){
    $active = Setting::where('name','redis')->first();
    if ($active && $active->value== 'on'){
      Redis::flushDB();
    }
  }

  function exists($key){
    $active = Setting::where('name','redis')->first();
    if ($active && $active->value== 'on'){
      return Redis::exists($key);
    } else {
      return false;
    }
  }

}