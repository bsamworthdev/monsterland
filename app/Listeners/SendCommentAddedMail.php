<?php

namespace App\Listeners;

use App\Events\CommentAdded;
use app\Repositories\DBUserRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\CommentedMonsterMailable;
use Illuminate\Support\Facades\Log;

class SendCommentAddedMail
{
    protected $DBUserRepo;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(DBUserRepository $DBUserRepo)
    {
        $this->DBUserRepo = $DBUserRepo;
        //
    }

    /**
     * Handle the event.
     *
     * @param  CommentAdded  $event
     * @return void
     */
    public function handle(CommentAdded $event)
    {
        $creators = $event->creators;
        $monster = $event->monster;

        foreach($creators as $creator_user_id){
            $creator = $this->DBUserRepo->find($creator_user_id,['permissions']);
            if ($creator->permissions && $creator->permissions->allow_monster_emails){
                Mail::to($creator->email)
                    ->send(new CommentedMonsterMailable($creator, $monster));
            }
        }
    }
}
