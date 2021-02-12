<?php

namespace App\Events;


use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentAdded
{
    use Dispatchable, SerializesModels;

    public $creators;
    public $monster;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($creators, $monster)
    {
        //
        $this->creators = $creators;
        $this->monster = $monster;
    }

}
