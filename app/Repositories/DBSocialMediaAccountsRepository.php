<?php

namespace app\Repositories;

use App\Models\SocialMediaAccount;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DBSocialMediaAccountsRepository{
  
  function update($user_id, $accounts) {

    foreach($accounts as $key => $value){
      // SocialMediaAccount::updateOrCreate(
      //   ['user_id' => $user_id],
      //   ['account_type' => $account->account_type],
      //   ['account_name' => $account->account_name]
      // );

      $acc = SocialMediaAccount::where('user_id', $user_id)
        ->where('account_type', $key)
        ->first();

      if(is_null($value)) {
          SocialMediaAccount::where('user_id', $user_id)
            ->where('account_type', $key)
            ->delete();
      } elseif ($acc !== null) {
        $acc->update(['account_name' => $value]);
      } else {
        $acc = SocialMediaAccount::create([
            'user_id' => $user_id,
            'account_type' => $key,
            'account_name' => $value
          ]);
      }

    }

  }
    

}