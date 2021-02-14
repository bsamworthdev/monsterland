<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use App\Mail\CommentedMonsterMailable;
use App\Models\Comment;
use App\Models\User;
use App\Models\CommentVote;
use App\Models\CommentSpam;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Repositories\DBUserRepository;
use App\Repositories\DBMonsterRepository;
use App\Repositories\DBMonsterSegmentRepository;
use App\Repositories\DBAuditRepository;
use App\Events\CommentAdded;

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
            $comment = Comment::create($request->all());

            $user_id = Auth::User()->id;
            $monster_id = $request->monster_id;
            $monster = $this->DBMonsterRepo->find($monster_id);
            $creators = $this->DBMonsterSegmentRepo->findSegmentCreators($monster_id, $user_id);
            
            //Emit commentAdded event
            event(new CommentAdded($creators, $monster, $comment));

            //Audit
            $this->DBAuditRepo->create($user_id, $monster_id, 'comment', ' commented on ');

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
                if($request->vote == "down"){
                    $vote--;
                }
                $comments->votes = $vote;
                $comments->save();
                if(CommentVote::create($data))
                    return "true";
            }
            if($type == "spam"){
                
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
            if($type == "nonspam"){
                
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
            if($type == "delete"){
                
                    $this->validate($request, [
                        'user_id' => 'required',
                    ]);
                    $comment = Comment::find($commentId);

                    if ($comment->user_id == $user_id || $user_id = 1){
                        $comment->deleted = 1;
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
                array_push($commentsData,[
                    "name" => $name,
                    "user_id" => $user->id,
                    //"photo_url" => (string)$photo,
                    "profilePic" => $profilePic,
                    "commentid" => $key->id,
                    "comment" => $key->comment,
                    "styled_comment" => $key->styled_comment,
                    "votes" => $key->votes,
                    "reply" => $reply,
                    "votedByUser" =>$vote,
                    "vote" => $voteStatus,
                    "deleted" => $key->deleted,
                    "spam" => $spam,
                    "replies" => $replies,
                    "date" => $key->created_at->toDateTimeString(),
                    "dateTidy" => date('jS M Y',strtotime($key->created_at->toDateTimeString()))
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
}