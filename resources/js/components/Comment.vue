<template>
    <div class="comments-app">
        <h3>Comments</h3>
        <!-- Comments List -->
        <div v-if="comments">
            <div class="comments mb-2" v-for="(comment,index) in commentsData" :key="comment.id">
                <!-- Comment -->
                <div v-if="!spamComments[index] || !comment.spam" :class="['comment',{'monsterified':comment.monsterified}]">
                    <!-- Comment Box -->
                    <div class="container comment-box pb-2">
                        <div class="row">
                            <div class="col-3 col-xl-1 col-sm-2 pr-0">
                                <a :href="'/monsters/' + comment.user_id" :title="comment.name">
                                    <img v-if="comment.profilePic && !profilePicBlocked(comment)" class="commentProfilePic border rounded mr-2 img-fluid" :src="'/storage/' + comment.profilePic.monster_id + '.png'">
                                    <img v-else class="commentProfilePic border rounded mr-2 img-fluid" :src="'/images/defaultProfile.png'">
                                </a>
                                <span class="comment-author d-block text-truncate" :title="comment.name">
                                    <em><a :href="'/monsters/' + comment.user_id">{{ comment.name}}</a></em>
                                </span>
                            </div>
                            <div class="col-9 col-xl-11 col-sm-10">
                                <div class="container">
                                    <div class="row">
                                        <div class="col-5">
                                            <div class="text-nowrap">
                                                <div class="comment-actions d-inline">
                                                    {{comment.votes}}
                                                </div>
                                                <div class="d-inline" v-if="user">
                                                    <a v-if="!comment.votedByUser && comment.user_id != user.id" class="pl-3 pr-2" @click="voteComment(comment.commentid,'directcomment',index,0,'up')">
                                                        <i class="fa fa-arrow-up"></i>
                                                    </a>
                                                    <a v-else class="pl-2 pr-2">
                                                        <i class="fa fa-arrow-up locked" :class="{'voted':comment.vote=='up'}" @click="undoVoteComment(comment.commentid,'directcomment',index, 'up')"></i>
                                                    </a>
                                                    <a v-if="!comment.votedByUser && comment.user_id != user.id" class="pl-2 pr-2" @click="voteComment(comment.commentid,'directcomment',index,0,'down')">
                                                        <i class="fa fa-arrow-down"></i>
                                                    </a>   
                                                    <a v-else class="pl-2 pr-1">
                                                        <i class="fa fa-arrow-down locked" :class="{'voted':comment.vote=='down'}" @click="undoVoteComment(comment.commentid,'directcomment',index, 'down')"></i>
                                                    </a> 
                                                </div>
                                                <div class="d-inline" v-else>
                                                    <i class="fa fa-arrow-up locked pl-2 pr-2" title="Log in to vote"></i>
                                                    <i class="fa fa-arrow-down locked pl-2 pr-2" title="Log in to vote"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-5">
                                            <div class="comment-date text-right">
                                                {{ comment.dateTidy}}
                                            </div>
                                        </div>
                                        <div class="col-1 text-right text-nowrap">
                                            <a v-if="user && comment.deleted == 0 && (user.is_patron || user.has_used_app) && (comment.user_id == user.id || user.id == 1)" @click="toggleMonsterFont(comment.commentid,'directcomment',index,0)">
                                                <i class="fa fa-pastafarianism pr-1 monsterifyButton" title="Monsterify My Comment!!"></i>
                                            </a>
                                            <a v-if="user && comment.deleted == 0 && (comment.user_id == user.id || user.id == 1)" @click="deleteComment(comment.commentid,'directcomment',index,0)">
                                                <i class="fa fa-times" title="Delete"></i>
                                            </a>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="comment-text text-break pt-2 pb-2">
                                                <div v-if="comment.deleted">[removed]</div>
                                                <div v-else v-html="styleComment(comment)"></div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-11 text-right">
                                            <div v-if="user && comment.user_id != user.id && user.moderator==1" class="comment-footer">
                                                <a @click="spamComment(comment.commentid,'directcomment',index,0)">
                                                    <i class="fa fa-ban"></i> Flag as spam/offensive
                                                </a>
                                                <a v-if="user.id == 1" @click="nonSpamComment(comment.commentid,'directcomment',index,0)" class="ml-2">
                                                    <i class="fa fa-check-circle"></i> Not Spam
                                                </a>
                                                <a @click="openComment(index)" class="d-none">Reply</a>
                                            </div>
                                        </div>
                                        <div class="col-1">
                                        </div>
                                    </div>
                                </div>
                             </div>
                        </div>
                    </div>
                    <!-- From -->
                    <div class="comment-form comment-v" v-if="user && commentBoxs[index]">
                        <form class="form" name="form">
                            <div class="form-row">
                                <textarea class="input form-control" placeholder="Add comment..." required v-model="message"></textarea>
                                <span class="input form-control" v-if="errorReply" style="color:red">{{errorReply}}</span>
                            </div>
                            <div class="form-row d-none">
                                <input class="input form-control" placeholder="Email" type="text" :value="user.name">
                            </div>
                            <div class="form-row">
                                <input type="button" class="btn btn-success" @click="replyComment(comment.commentid,index)" value="Add Comment">
                            </div>
                        </form>
                    </div>
                    <!-- Comment - Reply -->
                    <div v-if="comment.replies">
                        <div class="comments" v-for="(replies,index2) in comment.replies" :key="index2">
                            <div v-if="!spamCommentsReply[index2] || !replies.spam" class="comment reply">
                                <!-- Comment Box -->
                                <div class="comment-box" style="background: grey;">
                                    <div class="comment-text" style="color: white">{{replies.comment}}</div>
                                    <div class="comment-footer">
                                        <div class="comment-info">
                                            <span class="comment-author">
                                                    {{replies.name}}
                                                </span>
                                            <span class="comment-date">{{replies.date}}</span>
                                        </div>
                                        <div class="comment-actions">
                                            <ul class="list">
                                                <li>Total votes: {{replies.votes}}
                                                    <a v-if="!replies.votedByUser" @click="voteComment(replies.commentid,'replycomment',index,index2,'up')">
                                                        <i class="fa fa-arrow-up"></i>
                                                    </a>
                                                    <a v-if="!replies.votedByUser" @click="voteComment(comment.commentid,'replycomment',index,index2,'down')">
                                                        <i class="fa fa-arrow-down"></i>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a @click="spamComment(replies.commentid,'replycomment',index,index2)">Spam</a>
                                                </li>
                                                <li class="d-none">
                                                    <a @click="replyCommentBox(index2)">Reply</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <!-- From -->
                                <div class="comment-form reply" v-if="user && replyCommentBoxs[index2]">
                                    <form class="form" name="form">
                                        <div class="form-row">
                                            <textarea class="input form-control" placeholder="Add comment..." required v-model="message"></textarea>
                                            <span class="input form-control" v-if="errorReply" style="color:red">{{errorReply}}</span>
                                        </div>
                                        <div class="form-row d-none">
                                            <input class="input form-control" placeholder="Email" type="text" :value="user.name">
                                        </div>
                                        <div class="form-row">
                                            <input type="button" class="btn btn-success" @click="replyComment(comment.commentid,index)" value="Add Comment">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <form class="form" name="form" v-if="user">
            <div class="form-row">
                <textarea class="input form-control" placeholder="Add comment..." required v-model="message"></textarea>
                <span class="input form-control" v-if="errorComment" style="color:red">{{errorComment}}</span>
            </div>
            <div class="form-row d-none">
                <input class="input form-control" placeholder="Email" type="text" disabled :value="user.name">
            </div>
            <div class="form-row">
                <input type="button" :disabled="savingComment" class="btn btn-success" @click="saveComment" value="Add Comment">
            </div>
        </form>
    </div>
</template>

<script>
export default {
   props: {
       monsterId: Number,
       user: {
           default: null,
           type: Object
       },
       characters: Object
   },
   data() {
       return {
           comments: [],
           commentreplies: [],
           comments: 0,
           commentBoxs: [],
           message: null,
           replyCommentBoxs: [],
           commentsData: [],
           viewcomment: [],
           show: [],
           spamCommentsReply: [],
           spamComments: [],
           errorComment: null,
           errorReply: null,
           savingComment: false
       }
   },
   methods: {
       fetchComments() {
           axios.get('/comments/' + this.monsterId)
            .then((res) => {
                this.commentData = res.data;
                this.commentsData = _.orderBy(res.data, ['date'], ['asc']);
                this.comments = 1;
            })
            .catch((error) => {
                console.log(error);
            });
       },
       showComments(index) {
           if (!this.viewcomment[index]) {
               Vue.set(this.show, index, "hide");
               Vue.set(this.viewcomment, index, 1);
           } else {
               Vue.set(this.show, index, "view");
               Vue.set(this.viewcomment, index, 0);
           }
       },
       openComment(index) {
           if (this.user) {
               if (this.commentBoxs[index]) {
                   Vue.set(this.commentBoxs, index, 0);
               } else {
                   Vue.set(this.commentBoxs, index, 1);
               }
           }
       },
       replyCommentBox(index) {
           if (this.user) {
               if (this.replyCommentBoxs[index]) {
                   Vue.set(this.replyCommentBoxs, index, 0);
               } else {
                   Vue.set(this.replyCommentBoxs, index, 1);
               }
           }
       },
       saveComment() {
            if (this.message != null && this.message != ' ') {
                this.savingComment = true;
                this.errorComment = null;

                axios.post('/comments',{   
                    'monster_id': this.monsterId,
                    'comment': this.message,
                    'user_id': this.user.id  
                })
                .then((res) => {
                    if (res.data.status) {
                        this.$emit('commentAdded');
                        // this.commentsData.push({ 
                        //     "commentid": res.data.commentId, 
                        //     "user_id": this.user.id, 
                        //     "profilePic": this.user.profile_pic,
                        //     "name": this.user.name, 
                        //     "comment": this.message, 
                        //     "styled_comment": this.styleComment(this.message),
                        //     "votes": 0, 
                        //     "reply": 0, 
                        //     "replies": [] 
                        // });
                        // this.message = null;
                    }
                    this.savingComment=false;
                })
                .catch((error) => {
                    console.log(error);
                    this.savingComment=false;
                });

            } else {
                this.errorComment = "Please enter a comment to save";
            }
       },
       replyComment(commentId, index) {
           if (this.message != null && this.message != ' ') {
               this.errorReply = null;

                axios.post('/comments',{   
                    'comment': this.message,
                    'user_id': this.user.id,
                    'reply_id': commentId
                })
                .then((res) => {
                    if (res.data.status) {
                       if (!this.commentsData[index].reply) {
                           this.commentsData[index].replies.push({ "commentid": res.data.commentId, "name": this.user.name, "comment": this.message, "votes": 0 });
                           this.commentsData[index].reply = 1;
                           Vue.set(this.replyCommentBoxs, index, 0);
                           Vue.set(this.commentBoxs, index, 0);
                       } else {
                           this.commentsData[index].replies.push({ "commentid": res.data.commentId, "name": this.user.name, "comment": this.message, "votes": 0 });
                           Vue.set(this.replyCommentBoxs, index, 0);
                           Vue.set(this.commentBoxs, index, 0);
                       }
                       this.message = null;
                   }
                })
                .catch((error) => {
                    console.log(error);
                });


           } else {
               this.errorReply = "Please enter a comment to save";
           }
       },
       voteComment(commentId, commentType, index, index2, voteType) {
           if (this.user) {

                axios.post('/comments/' + commentId + '/vote', {
                   user_id: this.user.id,
                   vote: voteType 
                })
                .then(res => {
                   if (res.data) {
                       if (commentType == 'directcomment') {
                           if (voteType == 'up') {
                               this.commentsData[index].votes++;
                               this.commentsData[index].votedByUser = 1;
                               this.commentsData[index].vote = 'up';
                           } else if (voteType == 'down') {
                               this.commentsData[index].votes--;
                               this.commentsData[index].votedByUser = 1;
                               this.commentsData[index].vote = 'down';
                           }
                       } else if (commentType == 'replycomment') {
                           if (voteType == 'up') {
                               this.commentsData[index].replies[index2].votes++;
                           } else if (voteType == 'down') {
                               this.commentsData[index].replies[index2].votes--;
                           }
                       }
                   }
                })
                .catch((error) => {
                    console.log(error);
                });


           }
       },
       undoVoteComment(commentId, commentType, index, voteType) {
            if (this.user && this.commentsData[index].vote == voteType) {

                axios.post('/comments/' + commentId + '/undovote', {
                    user_id: this.user.id,
                })
                .then(res => {
                    if (res.data) {
                        if (commentType == 'directcomment') {
                            if (voteType == 'up') {
                               this.commentsData[index].votes--;
                            } else if (voteType == 'down') {
                                this.commentsData[index].votes++;
                            }
                            this.commentsData[index].votedByUser = 0;
                            this.commentsData[index].vote = '';
                        }
                    }
                })
                .catch((error) => {
                    console.log(error);
                });


            }
       },
       spamComment(commentId, commentType, index, index2) {
           console.log("spam here");
           if (this.user) {
                axios.post('/comments/' + commentId + '/spam', {
                    user_id: this.user.id,
                }).then(res => {
                    if (commentType == 'directcomment') {
                        Vue.set(this.spamComments, index, 1);
                        Vue.set(this.viewcomment, index, 1);
                        this.commentsData[index].spam=1;
                    } else if (commentType == 'replycomment') {
                        Vue.set(this.spamCommentsReply, index2, 1);
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
           }
        },
        nonSpamComment(commentId, commentType, index, index2) {
           if (this.user) {
                axios.post('/comments/' + commentId + '/nonspam', {
                    user_id: this.user.id,
                }).then(res => {
                    if (commentType == 'directcomment') {
                        Vue.set(this.spamComments, index, 1);
                        Vue.set(this.viewcomment, index, 1);
                        this.commentsData[index].spam=0;
                    } else if (commentType == 'replycomment') {
                        Vue.set(this.spamCommentsReply, index2, 1);
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
           }
        },
        deleteComment(commentId, commentType, index, index2) {
            console.log("delete here");
            if (this.user) {
                axios.post('/comments/' + commentId + '/delete', {
                    user_id: this.user.id,
                }).then(res => {
                    if (commentType == 'directcomment') {
                        this.commentsData[index].deleted=1;
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
            }
        },
        toggleMonsterFont: function(commentId, commentType, index, index2) {
            if (this.user) {
                var val = this.commentsData[index].monsterified ? 0 : 1;
                axios.post('/comments/' + commentId + '/setmonsterified', {
                    user_id: this.user.id,
                    monsterified: val
                }).then(res => {
                    if (commentType == 'directcomment') {
                        this.commentsData[index].monsterified= val;
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
            }
        },
        styleComment: function(comment){
            var text = comment.styled_comment ? comment.styled_comment : comment.comment;
            var text = text.replaceAll('www.monsterland.net','monsterland.net');
            var monster_ids = [...text.matchAll(/\(https:\/\/monsterland.net\/gallery\/(\d+)/g)];
            var new_comment = text.replace(/\[(.*?)\]\((.*?)\)/g,'<a target="_blank" href="$2">$1</a>');
            if (monster_ids) {
                for(var i=0; i<monster_ids.length; i++){
                    new_comment += '' + 
                        '<a target="_blank" href="/gallery/' + monster_ids[i][1] + '">' +
                            '<img class="previewImage mt-1 d-block border border-dark" style="width:90px;" src="/storage/' + monster_ids[i][1] + '_thumb.png">' + 
                        '</a>';
                }
            }
            
            if (comment.monsterified) {
                for(var i=0; i<this.characters.length; i++) {
                    var character = this.characters[i].character;
                    var monster_id = this.characters[i].monster_id;
                    var regEx = new RegExp("(" + character + ")(?!([^<]+)?>)", "gi");
                    new_comment = new_comment.replace(regEx, '<a href="/gallery/' + monster_id + '" title="' + character + '">' +
                            '<img class="characterImage" src="/storage/characters/' + monster_id + '_tiny.png">' +
                        '</a>');
                }
                
                //Replace linebreaks
                regEx = new RegExp("(\\n)(?!([^<]+)?>)", "gi");
                new_comment = new_comment.replace(regEx, ' <br> ');
                
                var str = '';
                var regEx = new RegExp("([ ]|\\n)(?!([^<]+)?>)", "gi");
                new_comment.split(regEx).forEach(function(word) {
                    if (word && word !=' ' && word !='<br>'){
                        str += '<span class="word">' + word + '</span>';
                    } else if(word) {
                        str += word;
                    }
                });
                
                // //Replace linebreaks
                // regEx = new RegExp("(\\n)(?!([^<]+)?>)", "gi");
                // new_comment = new_comment.replace(regEx, '</br>');

                new_comment = str;
                // Increase space size
                // var regEx = new RegExp("([ ])(?!([^<]+)?>)", "gi");
                // new_comment = new_comment.replace(regEx, '<span style="white-space:break-spaces;">&nbsp;&nbsp;&nbsp;</span>');
        

                
            
            }

            return new_comment;
        },
        profilePicBlocked: function(comment){
            return (comment.profilePic.monster.nsfw && (!this.user || !this.user.allow_nsfw));
        }
   },
   mounted() {
      console.log("mounted");
      this.fetchComments();
   }
}
</script>
<style>
.comments-app .img-fluid {
    max-width: 100%;
    height: auto;
    max-height:40px;
}
.comments-app .comment-box {
    border:1px solid rgba(0, 0, 0, 0.125);
    border-radius:13px;
    padding-top:5px;
    padding-left:15px;
    padding-right:15px;
}
.comments-app .comment-date{
    font-size:10px;
    font-style:italic;
    color:#C0C0C0;
}
.comments-app .comment-author{
    font-style:italic;
    color:#C0C0C0;
    font-size:14px;
    white-space:nowrap;
    min-width:74px;
}
.comments-app .fa-arrow-up{
    color:green;
    cursor:pointer;
}
.comments-app .fa-arrow-down{
    color:red;
    cursor:pointer;
}
.comments-app .fa-arrow-up.locked, .comments-app .fa-arrow-down.locked{
    opacity: 0.2;
    color:black;
}
.comments-app .fa-arrow-up.voted, .comments-app .fa-arrow-down.voted{
    color:blue;
}
.comments-app .fa-ban, .comments-app .fa-times{
    color:red;
    cursor:pointer;
}
.comments-app .monsterifyButton{
    color:grey;
    cursor:pointer;
}
.comments-app .comment.monsterified .monsterifyButton{
    color:black;
}
.comments-app .comment-footer{
    font-size:11px;
}
.comments-app .comment-text{
    white-space:pre-wrap;
}
/* .comments-app .comment.monsterified .comment-text{
    white-space:nowrap!important;
} */
.comments-app .comment-footer a{
    cursor:pointer;
}
.comments-app .previewImage{
    display:block;
    border:1px solid rgba(0, 0, 0, 0.125);
    width: 90px;
}
.comments-app .characterImage{
    height:20px; 
    border-radius:2px;
    cursor:pointer;
    border:1px solid #4b4b4b;
    margin-bottom:6px;
}
.comments-app .word{
    padding-right:10px;
    display:inline-block;
}
</style>