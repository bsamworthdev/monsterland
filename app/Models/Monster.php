<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\MonsterTrait;
use Carbon\Carbon;

class Monster extends Model
{
    use MonsterTrait;
    use HasFactory;

    protected $table = 'monsters';
    protected $with = array('segments', 'ratings');
    protected $appends = array('created_at_tidy');

    public function getCreatedAtTidyAttribute()
    {
        return Carbon::parse($this->created_at)->diffForHumans();
    }
}
