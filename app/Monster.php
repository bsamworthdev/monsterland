<?php

namespace App;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Monster extends Model
{
    use Traits\MonsterTrait;

    protected $table = 'monsters';
    protected $with = array('segments', 'ratings');

}
