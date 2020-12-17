<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockedIP extends Model
{
    use HasFactory;
    protected $table = 'blocked_ips';
}
