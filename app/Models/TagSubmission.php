<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagSubmission extends Model
{
    use HasFactory;
    protected $table = 'tag_submissions';
    protected $fillable = ['name','user_id','monster_id'];
}
