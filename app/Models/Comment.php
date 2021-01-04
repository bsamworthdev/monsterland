<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'comments';
    protected $fillable = ['comment','votes','spam','deleted','reply_id','monster_id','user_id'];
    protected $dates = ['created_at', 'updated_at'];
    protected $appends = ['created_at_date', 'styled_comment'];
    protected $with = array('monster');

    public function replies()
    {
        return $this->hasMany('App\Models\Comment','id','reply_id');
    }

    public function getCreatedAtDateAttribute()
    {
        return date( 'jS M Y', strtotime($this->created_at));
    }

    public function monster()
    {
        return $this->hasOne('App\Models\Monster','id','monster_id')
            ->select(['id', 'name']);
    }

    public function getStyledCommentAttribute()
    {
        $comment = $this->comment;
        $words = explode(" ", $comment);
        $styledComment = '';
        $linkCount=0;
        foreach ($words as $word){
            if ($styledComment != '') $styledComment .= ' ';
            if (strpos($word, 'http') === 0){
                $styledComment .= "[".$word."](".$word.")";
            } else {
                $styledComment .= htmlentities($word);
            }
        }

        return $styledComment;
    }

}