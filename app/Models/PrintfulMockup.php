<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrintfulMockup extends Model
{
    use HasFactory;

    protected $table = 'printful_mockups';
    protected $fillable = ['monster_id', 'url', 'description'];
}
