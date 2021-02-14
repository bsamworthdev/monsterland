<?php

namespace App\Events;


use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentAdded
{
    use Dispatchable, SerializesModels;

    public $creators;
    public $monster;
    public $comment;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($creators, $monster, $comment)
    {
        //
        $this->creators = $creators;
        $this->monster = $monster;
        $this->comment = $comment;
    }

}
