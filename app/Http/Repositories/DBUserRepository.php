<?php

namespace app\http\Repositories;

use App\User;

class DBUserRepository{

  function find($user_id){
    return User::find($user_id); 
  }
}