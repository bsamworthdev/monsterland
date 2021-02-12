<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MonsterCompleted
{
    use Dispatchable, SerializesModels;

    public $monster;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($monster)
    {
        //
        $this->monster = $monster;
    }
}
