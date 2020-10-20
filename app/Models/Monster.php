<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\MonsterTrait;

class Monster extends Model
{
    use MonsterTrait;
    use HasFactory;

    protected $table = 'monsters';
    protected $with = array('segments', 'ratings');

}
