<template>
    <div class="comments-app">
        <h3>Comments</h3>
        <!-- Comments List -->
        <div v-if="comments">
            <div class="comments mb-2" v-for="(comment,index) in commentsData" :key="comment.id">
                <!-- Comment -->
                <div v-if="!spamComments[index] || !comment.spam" class="comment">
                    <!-- Comment Box -->
                    <div class="container comment-box">
                        <div class="row">
                            <div class="col-6 col-xl-2 ">
                                <span class="comment-author">
                                    <em>{{ comment.name}}</em>
                                </span>
                            </div>
                            <div class="col-6 col-xl-4 ">
                                <div class="text-nowrap">
                                    <div class="comment-actions d-inline">
                                        {{comment.votes}}
                                    </div>
                                    <a v-if="!comment.votedByUser && comment.user_id != user.id" class="pl-1 pr-1" @click="voteComment(comment.commentid,'directcomment',index,0,'up')">
                                        <i class="fa fa-arrow-up"></i>
                                    </a>
                                    <a v-else class="pl-1 pr-1">
                                        <i class="fa fa-arrow-up locked" :class="{'voted':comment.vote=='up'}"></i>
                                    </a>
                                    <a v-if="!comment.votedByUser && comment.user_id != user.id" @click="voteComment(comment.commentid,'directcomment',index,0,'down')">
                                        <i class="fa fa-arrow-down"></i>
                                    </a>   
                                    <a v-else class="pl-1 pr-1">
                                        <i class="fa fa-arrow-down locked" :class="{'voted':comment.vote=='down'}"></i>
                                    </a> 
                                </div>
                            </div>
                            <div class="col-11 col-xl-5 ">
                                <div class="comment-date text-right">
                                    {{ comment.dateTidy}}
                                </div>
                            </div>
                            <div class="col-1 col-xl-1 text-right">
                                <a v-if="comment.user_id == user.id && comment.deleted == 0" @click="deleteComment(comment.commentid,'directcomment',index,0)">
                                    <i class="fa fa-times" title="Delete"></i>
                                </a>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <div class="comment-text">
                                    <div v-if="comment.deleted">[removed]</div>
                                    <div v-else>{{comment.comment}}</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-11 text-right">
                                <div v-if="comment.user_id != user.id && user.id==1" class="comment-footer">
                                    <a @click="spamComment(comment.commentid,'directcomment',index,0)">
                                        <i class="fa fa-ban"></i> Flag as spam
                                    </a>
                                    <a @click="openComment(index)" class="d-none">Reply</a>
                                </div>
                            </div>
                            <div class="col-1">
                            </div>
                        </div>
                    </div>
                    <!-- From -->
                    <div class="comment-form comment-v" v-if="commentBoxs[index]">
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
                                <div class="comment-form reply" v-if="replyCommentBoxs[index2]">
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
        <form class="form" name="form">
            <div class="form-row">
                <textarea class="input form-control" placeholder="Add comment..." required v-model="message"></textarea>
                <span class="input form-control" v-if="errorComment" style="color:red">{{errorComment}}</span>
            </div>
            <div class="form-row d-none">
                <input class="input form-control" placeholder="Email" type="text" disabled :value="user.name">
            </div>
            <div class="form-row">
                <input type="button" class="btn btn-success" @click="saveComment" value="Add Comment">
            </div>
        </form>
    </div>
</template>

<script>
var _ = require('lodash');
export default {
   props: {
       monsterId: Number,
       user: Object
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
           componentKey: 0,
       }
   },
   methods: {
       fetchComments() {
           axios.get('/comments/' + this.monsterId)
            .then((res) => {
                this.commentData = res.data;
                this.commentsData = _.orderBy(res.data, ['votes'], ['desc']);
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
               this.errorComment = null;

                axios.post('/comments',{   
                    'monster_id': this.monsterId,
                    'comment': this.message,
                    'user_id': this.user.id  
                })
                .then((res) => {
                    if (res.data.status) {
                        this.commentsData.push({ 
                            "commentid": res.data.commentId, 
                            "user_id": this.user.id, 
                            "name": this.user.name, 
                            "comment": this.message, 
                            "votes": 0, 
                            "reply": 0, 
                            "replies": [] 
                        });
                        this.message = null;
                    }
                })
                .catch((error) => {
                    console.log(error);
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
        }
   },
   mounted() {
      console.log("mounted");
      this.fetchComments();
   }
}
</script>
<style scoped>
.comment-box {
    border:1px solid rgba(0, 0, 0, 0.125);
    border-radius:13px;
    padding-top:5px;
    padding-left:15px;
    padding-right:15px;
}
.comment-date{
    font-size:10px;
    font-style:italic;
    color:#C0C0C0;
}
.comment-author{
    font-style:italic;
    color:#C0C0C0;
}
.fa-arrow-up{
    color:green;
}
.fa-arrow-down{
    color:red;
}
.fa-arrow-up.locked, .fa-arrow-down.locked{
    opacity: 0.2;
    color:black;
}
.fa-arrow-up.voted, .fa-arrow-down.voted{
    color:blue;
}
.fa-arrow-down{
    color:red;
}

.fa-ban, .fa-times{
    color:red;
    cursor:pointer;
}
.comment-footer{
    font-size:11px;
}

</style>