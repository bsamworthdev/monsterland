<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InfoMessageClosed extends Model
{
    protected $table = 'info_messages_closed';

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function info_message()
    {
        return $this->belongsTo('App\InfoMessage');
    }
}
