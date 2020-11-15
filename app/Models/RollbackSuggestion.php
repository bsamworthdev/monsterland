<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RollbackSuggestion extends Model
{
    protected $table = 'rollback_suggestions';

    protected $fillable = [
        'monster_id', 'requested_by', 'status'
    ];
}
