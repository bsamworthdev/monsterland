<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'permissions';

    protected $fillable = [
        'user_id', 'allow_monster_emails', 'allow_feature_emails'
    ];
}
