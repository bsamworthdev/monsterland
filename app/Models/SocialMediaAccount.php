<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialMediaAccount extends Model
{
    use HasFactory;

    protected $table = 'social_media_accounts';
    protected $fillable = ['user_id', 'account_type', 'account_name'];
}
