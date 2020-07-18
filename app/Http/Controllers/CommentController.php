<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Comment;
use App\User;
use App\CommentVote;
use App\CommentSpam;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    public function __construct()
    {
        // $this->middleware(['auth','verified']);
    }

    /**
    * Store a newly created resource in storage.
    */
    public function store(Request $request)
    {
        $this->validate($request, [
            'comment' => 'required',
            'reply_id' => 'filled',
            'monster_id' => 'filled',
            'user_id' => 'required',
        ]);
        $comment = Comment::create($request->all());

        if($comment){
            return [ "status" => "true","commentId" => $comment->id ];
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
       Log::info('update triggered');
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
           if($request->vote == "up"){
               $comment = $comments->first();
               $vote = $comment->votes;
               $vote++;
               $comments->votes = $vote;
               $comments->save();
           }
           if($request->vote == "down"){
               $comment = $comments->first();
               $vote = $comment->votes;
               $vote--;
               $comments->votes = $vote;
               $comments->save();
           }
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
       if($type == "delete"){
          
            $this->validate($request, [
                'user_id' => 'required',
            ]);
            $comment = Comment::find($commentId);
            $comment->deleted = 1;
            $comment->save();
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
            $replies = $this->replies($key->id);
            $photo = $user->first()->photo_url;
            // dd($photo->photo_url);
            $reply = 0;
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
            }          
            if(count($replies) > 0){
                $reply = 1;
            }
            if(!$spam){
                array_push($commentsData,[
                    "name" => $name,
                    "user_id" => $user->id,
                    "photo_url" => (string)$photo,
                    "commentid" => $key->id,
                    "comment" => $key->comment,
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
        return $collection->sortBy('votes');
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
       return $collection->sortBy('votes');
   }  
}