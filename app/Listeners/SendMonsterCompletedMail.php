<?php

namespace App\Listeners;

use App\Events\MonsterCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\CompletedMonsterMailable;
use app\Repositories\DBUserRepository;
use Illuminate\Support\Facades\Log;


class SendMonsterCompletedMail
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
     * @param  MonsterCompleted  $event
     * @return void
     */
    public function handle(MonsterCompleted $event)
    {
        $monster = $event->monster;
        foreach($monster->segments as $segment){
            if ($segment->email_on_complete){
                $segment_user_id = $segment->created_by;
                if ($segment_user_id > 0){
                    $segment_user= $this->DBUserRepo->find($segment_user_id);
                    Mail::to($segment_user->email)
                        ->send(new CompletedMonsterMailable($segment_user, $monster));
                }
            }
        }
    }
}
