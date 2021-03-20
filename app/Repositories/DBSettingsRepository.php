<?php

namespace app\Repositories;

use App\Models\Setting;
use Illuminate\support\Facades\DB;

class DBSettingsRepository{

  function getValue($name){
    $values = Setting::where('name', $name)->first();
    return $values->value;
  }

  function everyoneCanUseStore(){
    return $this->getValue('store_setting') === 'everyone';
  }
}