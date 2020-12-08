<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TakeTwoRequest extends Model
{
    use HasFactory;
    protected $table = 'take_two_requests';
    protected $fillable = ['user_id','monster_id', 'requested_by', 'from_segment', 'status'];
}
