<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use App\Mail\CommentedMonsterMailable;
use App\Models\Comment;
use App\Models\User;
use App\Models\CommentVote;
use App\Models\CommentSpam;
use App\Models\Profanity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Repositories\DBUserRepository;
use App\Repositories\DBMonsterRepository;
use App\Repositories\DBMonsterSegmentRepository;
use App\Repositories\DBAuditRepository;
use App\Events\CommentAdded;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    protected $DBUserRepo;
    protected $DBMonsterRepo;
    protected $DBMonsterSegmentRepo;
    protected $DBAuditRepo;

    public function __construct(DBUserRepository $DBUserRepo, 
    DBMonsterRepository $DBMonsterRepo,
    DBMonsterSegmentRepository $DBMonsterSegmentRepo,
    DBAuditRepository $DBAuditRepo)
    {
        // $this->middleware(['auth','verified']);
        $this->DBUserRepo = $DBUserRepo;
        $this->DBMonsterRepo = $DBMonsterRepo;
        $this->DBMonsterSegmentRepo = $DBMonsterSegmentRepo;
        $this->DBAuditRepo = $DBAuditRepo;
    }

    /**
    * Store a newly created resource in storage.
    */
    public function store(Request $request)
    {
        if (Auth::check()){

            $this->validate($request, [
                'comment' => 'required',
                'reply_id' => 'filled',
                'monster_id' => 'filled',
                'user_id' => 'required',
            ]);

            $user_id = Auth::User()->id;
            $monster_id = $request->monster_id;

            //Limit to 50 comments total per monster
            $commentCount = Comment::where('monster_id', $monster_id)
                ->where('deleted',0)
                ->count();
            if ($commentCount > 50) return ["status" => "false"];

            //Limit users to 10 comments per monster
            $userCommentCount = Comment::where('monster_id', $monster_id)
                ->where('user_id', $user_id)
                ->where('deleted',0)
                ->count();
            if ($userCommentCount > 10) return ["status" => "false"];

            $commentObject = $request->all();
            $commentObject['comment']= strip_tags($commentObject['comment']);
            $comment = Comment::create($commentObject);

            $monster = $this->DBMonsterRepo->find($monster_id);
            $creators = $this->DBMonsterSegmentRepo->findSegmentCreators($monster_id, $user_id);
            
            //Emit commentAdded event
            event(new CommentAdded($creators, $monster, $comment, $user_id));

            //Audit
            $this->DBAuditRepo->create($user_id, $monster_id, 'comment', ' commented on ');

            //Link current user with this monster
            DB::table('user_linked_monsters')->updateOrInsert([
                'user_id' => $user_id, 
                'monster_id' => $monster_id,
            ],[
                'user_id' => $user_id, 
                'monster_id' => $monster_id,
                'role' => 'commenter',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            if($comment){
                return [ "status" => "true","commentId" => $comment->id ];
            }
        }
    }

    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  $commentId
    * @param  $type
    * @return \Illuminate\Http\Response
    */
   public function update(Request $request, $commentId,$type)
   {
        if (Auth::check()){
            $user_id = Auth::User()->id;
            if($type == "vote"){          
                $this->validate($request, [
                'vote' => 'required',
                'user_id' => 'required',
                ]);
                
                $alreadyVoted= CommentVote::where('comment_id',$commentId)
                    ->where('user_id',$user_id)
                    ->count() > 0;
                if ($alreadyVoted) return false;

                $comments = Comment::find($commentId);
                $data = [
                    "comment_id" => $commentId,
                    'vote' => $request->vote,
                    'user_id' => $request->user_id,
                ];
                $vote = $comments->votes;

                if($request->vote == "up"){
                    $vote++;
                }
                elseif($request->vote == "down"){
                    $vote--;
                }
                $comments->votes = $vote;
                $comments->save();
                if(CommentVote::create($data))
                    return "true";
            }
            elseif($type == "spam"){
                
                $this->validate($request, [
                    'user_id' => 'required',
                ]);
                $comments = Comment::find($commentId);
                $comment = $comments->first();
                $spam = $comment->spam;
                $spam++;
                $comments->spam = $spam;
                $comments->save();
                $data = [
                    "comment_id" => $commentId,
                    'user_id' => $request->user_id,
                ];
                if(CommentSpam::create($data))
                    return "true";
            }
            elseif($type == "nonspam"){
                
                $this->validate($request, [
                    'user_id' => 'required',
                ]);
                if ($user_id != 1) return false;
 
                $comment_spam = CommentSpam::where('comment_id',$commentId);
                if($comment_spam){
                    $comment_spam->delete();

                    $comments = Comment::find($commentId);
                    $comment = $comments->first();
                    $spam = $comment->spam;
                    $spam--;
                    $comments->spam = $spam;
                    $comments->save();
                    return "true";
                }
            }
            elseif($type == "delete"){
                
                    $this->validate($request, [
                        'user_id' => 'required',
                    ]);
                    $comment = Comment::find($commentId);

                    if ($comment->user_id == $user_id || $user_id = 1){
                        $comment->deleted = 1;
                        $comment->save();
                    }
            }
            elseif($type == "undovote"){   
                $this->validate($request, [
                    'user_id' => 'required',
                ]); 
                $currentVotes = CommentVote::where('comment_id',$commentId)
                    ->where('user_id',$user_id);

                if ($currentVotes->count() == 0) return false;
                $currentVote = $currentVotes->get()->first();

                $comments = Comment::find($commentId);
                $data = [
                    "comment_id" => $commentId,
                    'vote' => $request->vote,
                    'user_id' => $request->user_id,
                ];
                $vote = $comments->votes;
                if($currentVote->vote == "up"){
                    $vote--;
                }
                if($currentVote->vote == "down"){
                    $vote++;
                }
                $comments->votes = $vote;
                $comments->save();
                if($currentVotes->delete())
                    return "true";
            }
            elseif($type == "setmonsterified"){
                $this->validate($request, [
                    'user_id' => 'required',
                    'monsterified' => 'required'
                ]);
                $comment = Comment::find($commentId);
                $user = User::find($user_id);

                if (($user->is_patron || $user->has_used_app) && ($comment->user_id == $user_id || $user_id = 1)){
                    $comment->monsterified = $request->monsterified;
                    $comment->save();
                }
        }
        }
   }

   /**
    * Get Comments for monsterId
    *
    * @return Comments
    */
    public function index($monsterId)
    {
        //
        $comments = Comment::where('monster_id',$monsterId)->get();

        $commentsData = [];
        foreach ($comments as $key) {
            $user = User::find($key->user_id);
            $name = $user->name;
            $profilePic = $user->profilePic;
            $replies = $this->replies($key->id);
            //$photo = $user->first()->photo_url;
            // dd($photo->photo_url);
            $reply = 0;
            $vote = 0;
            $voteStatus = 0;
            $spam = 0;
            if(Auth::user()){
                $user_id = Auth::user()->id;
                $voteByUser = CommentVote::where('comment_id',$key->id)->where('user_id',$user_id)->first();
                if ($user_id == 1) {   
                    $spamComment = false;
                } else {
                    $spamComment = CommentSpam::where('comment_id',$key->id)->first();
                }
                if($voteByUser){
                    $vote = 1;
                    $voteStatus = $voteByUser->vote;
                }
                if($spamComment){
                    $spam = 1;
                }
            } else {
                $vote = 0;
                $voteStatus = 0;
                $spam = 0;
            }
            if(count($replies) > 0){
                $reply = 1;
            }
            if(!$spam){
                $comment = $key->comment;
                $styled_comment = $key->styled_comment;
                if (!Auth::check() || !Auth::user()->allow_nsfw){
                    $comment = $this->censorProfanities($comment);
                    $styled_comment = $this->censorProfanities($styled_comment);
                }
                array_push($commentsData,[
                    "name" => $name,
                    "user_id" => $user->id,
                    //"photo_url" => (string)$photo,
                    "profilePic" => $profilePic,
                    "commentid" => $key->id,
                    "comment" => $comment,
                    "styled_comment" => $styled_comment,
                    "votes" => $key->votes,
                    "reply" => $reply,
                    "votedByUser" =>$vote,
                    "vote" => $voteStatus,
                    "deleted" => $key->deleted,
                    "spam" => $spam,
                    "replies" => $replies,
                    "date" => $key->created_at->toDateTimeString(),
                    "dateTidy" => date('jS M Y',strtotime($key->created_at->toDateTimeString())),
                    "monsterified" => $key->monsterified
                ]);
            }       
        }
        $collection = collect($commentsData);

        return $collection->sortBy('date',true);
    }
    protected function replies($commentId)
    {
       $comments = Comment::where('reply_id',$commentId)->get();
       $replies = [];
       foreach ($comments as $key) {
           $user = User::find($key->user_id);
           $name = $user->name;
           $photo = $user->first()->photo_url;
           $vote = 0;
           $voteStatus = 0;
           $spam = 0;        
           if(Auth::user()){
               $voteByUser = CommentVote::where('comment_id',$key->id)->where('user_id',Auth::user()->id)->first();
               $spamComment = CommentSpam::where('comment_id',$key->id)->where('user_id',Auth::user()->id)->first();
               if($voteByUser){
                   $vote = 1;
                   $voteStatus = $voteByUser->vote;
               }
               if($spamComment){
                   $spam = 1;
               }
           } else{
                $voteStatus = 0;
                $spam = 0;
           }
           if(!$spam){        
               array_push($replies,[
                   "name" => $name,
                   "user_id" => $user->id,
                   "photo_url" => $photo,
                   "commentid" => $key->id,
                   "comment" => $key->comment,
                   "votes" => $key->votes,
                   "votedByUser" => $vote,
                   "vote" => $voteStatus,
                   "spam" => $spam,
                   "deleted" => $key->deleted,
                   "date" => $key->created_at->toDateTimeString(),
                   "dateTidy" => date('jS M Y',strtotime($key->created_at->toDateTimeString()))
               ]);
           }
       }
       $collection = collect($replies);
       return $collection->sortBy('date');
   }  

   function censorProfanities($comment){
        $profanities = Profanity::all()->pluck('word')->toArray();
        $censored_profanities = [];
        foreach ($profanities as $profanity){
            $len = strlen($profanity);
            $censored_profanities[] = substr($profanity, 0, 1).str_repeat('*', $len - 2).substr($profanity, $len - 1, 1);
        }
        $comment = str_replace($profanities, $censored_profanities, $comment);
        return $comment;
   }
}