<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InfoMessage extends Model
{
    //
    protected $table = 'info_messages';

    public function closed_info_messages()
    {
        return $this->hasMany('App\InfoMessageClosed', 'info_message_id', 'id');
    }
}
