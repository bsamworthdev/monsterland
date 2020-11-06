<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $table = 'orders';
    protected $with = array('address');

    protected $fillable = [
        'user_id', 'book_id', 'status','quantity','total_cost'
    ];

    public function address()
    {
        return $this->hasOne('App\Models\OrderAddress', 'order_id', 'id');
    }
}
