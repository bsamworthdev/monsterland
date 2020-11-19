<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InfoMessage extends Model
{
    //
    protected $table = 'info_messages';
    protected $fillable = ['text','user','style','start_date','end_date'];

    public function closed_info_messages()
    {
        return $this->hasMany('App\Models\InfoMessageClosed', 'info_message_id', 'id');
    }
}
