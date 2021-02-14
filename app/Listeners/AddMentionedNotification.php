<?php

namespace App\Listeners;

use App\Events\CommentAdded;
use App\Repositories\DBUserRepository;
use App\Repositories\DBAuditRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AddMentionedNotification
{
    protected $DBUserRepo;
    protected $DBAuditRepo;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(DBUserRepository $DBUserRepo, DBAuditRepository $DBAuditRepo)
    {
        $this->DBUserRepo = $DBUserRepo;
        $this->DBAuditRepo = $DBAuditRepo;
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
        $user_id = Auth::User()->id;
        $monster = $event->monster;
        $comment = $event->comment;

        $words = preg_split('/\s+/', $comment->comment);
        foreach ($words as $word){
           if (strpos($word, '@') === 0 && strlen($word) > 1) {
                //Get mentioned users
                $word_nospaces =str_replace(' ', '_', $word);
                $word_nospaces = rtrim($word_nospaces, ',');
                $word_nospaces = rtrim($word_nospaces, '.');

                $user = $this->DBUserRepo->findUserByName(htmlentities(substr($word_nospaces, 1)));
                if ($user && $user->id){
                    $this->DBAuditRepo->create($user_id, $monster->id, 'mention', ' mentioned ', $user->id);
                }
            }  
        }
        
    }
}
