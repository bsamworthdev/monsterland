<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InfoMessageClosed extends Model
{
    protected $table = 'info_messages_closed';

    public function user()
    {
        return $this->belongsTo('App\Models\User')
            ->select(['id', 'name']);;
    }

    public function info_message()
    {
        return $this->belongsTo('App\Models\InfoMessage');
    }
}
