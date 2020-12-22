<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">

                <div class="card">
                    <div class="card-header">
                        <div class="container monster-header" :class="{'closed':monster.status=='cancelled'}">
                            <div class="row">
                                <div class="col-6">
                                    <button class="btn btn-info btn-block" :disabled="lockPrev" @click="prevClick">
                                        <i class="fas fa-arrow-left"></i> <span class="btnLabel">Previous</span>
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button class="btn btn-info btn-block" :disabled="lockNext" @click="nextClick">
                                        <span class="btnLabel">Next</span> <i class="fas fa-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="row mt-3" :class="{'redTitle':monster.nsfl||monster.nsfw||monster.suggest_rollback}">
                                <div class="col-12">
                                    <h1>
                                        {{ monster.name }}
                                        <span v-if="monster.nsfl">(NSFL)</span>
                                        <span v-else-if="monster.nsfw">(NSFW)</span>
                                        <span v-else-if="monster.suggest_rollback">(FLAGGED)</span>
                                    </h1>
                                </div>
                            </div>
                            <div v-if ="!groupMode">
                                <div v-if="!user" class="row">
                                    <div class="col-6 text-right">
                                        <h4>Overall Rating {{ overallRating }}</h4>
                                    </div>
                                    <div class="col-6 text-left">
                                        <button class="btn btn-success" onclick="location.href='/login'">
                                            Sign in to rate
                                        </button>
                                    </div>
                                </div>
                                <div v-else-if="userIsCreator()" class="row">
                                    <div class="ratingContainer col-12 col-sm-12 col-md-6 pr-0">
                                        <h4>Overall Rating: <b>{{ overallRating }}</b></h4>
                                    </div>
                                    <div class="votesContainer col-sm-12 col-md-6 pl-3">
                                        <h4>{{ voteCount }}</h4>
                                    </div>
                                </div>
                                <div v-else-if="myRating > 0" class="row">
                                    <div class="ratingContainer col-sm-12 col-md-6 pr-0">
                                        <h4>Overall Rating: <b>{{ overallRating }}</b></h4>
                                    </div>
                                    <div class="votesContainer col-sm-12 col-md-6 pl-3">
                                        <h4>{{ voteCount }}</h4>
                                    </div>
                                    <div class="col-12 text-center">
                                        <p class="mb-2">(You rated this {{ myRating }})</p>
                                    </div>
                                </div>
                                <div v-else class="row ratingRow">
                                    <div class="col-sm-12 col-md-3">
                                        Rate this monster:
                                    </div>
                                    <div class="col-sm-12 col-md-6">
                                        <div class="slidecontainer">
                                            
                                            <div class="form-group"> 
                                                <input type="range" class="form-control-range" id="formControlRange" min="1" max="10" v-model="selectedRating">
                                            </div>
                                            
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-1">
                                        {{ selectedRating }}
                                    </div>
                                    <div class="col-sm-6 col-md-2">
                                        <button class="btn btn-success btn-sm btn-block" @click="saveRating">
                                            Save
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-1">
                                <div class="col-4">
                                    <h5>Head: 
                                        <a v-if="getCreator('head').id != 0" 
                                        :href="'/monsters/' + getCreator('head').id ">
                                            <b>{{ getCreator('head').name }} <i title="pro user" v-if="getCreator('head').vip" class="fa fa-star"></i></b>
                                        </a>
                                        <b v-else-if="getCreatorGroupUserName('head')">{{ getCreatorGroupUserName('head') }}</b>
                                        <b v-else>GUEST</b>
                                    </h5>
                                </div>
                                <div class="col-4 ">
                                    <h5>Body:
                                        <a v-if="getCreator('body').id != 0" 
                                        :href="'/monsters/' + getCreator('body').id ">
                                            <b>{{ getCreator('body').name }} <i title="pro user" v-if="getCreator('body').vip" class="fa fa-star"></i></b>
                                        </a>
                                        <b v-else-if="getCreatorGroupUserName('body')">{{ getCreatorGroupUserName('body') }}</b>
                                        <b v-else>GUEST</b>
                                    </h5>
                                </div>
                                <div class="col-4">
                                    <h5>Legs: 
                                        <a v-if="getCreator('legs').id != 0" 
                                        :href="'/monsters/' + getCreator('legs').id ">
                                            <b>{{ getCreator('legs').name }} <i title="pro user" v-if="getCreator('legs').vip" class="fa fa-star"></i></b>
                                        </a>
                                        <b v-else-if="getCreatorGroupUserName('legs')">{{ getCreatorGroupUserName('legs') }}</b>
                                        <b v-else>GUEST</b>
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="canvas_container" :class="{'closed':monster.status=='cancelled','useImage':monster.image && monster.image != 'n/a'}" class="card-body">
                        <div v-if="monster.image && monster.image != 'n/a'" class="container">
                            <div class="row">
                                <img :src="monster.image">
                            </div>
                        </div>
                        <div v-else class="container">
                             <div class="row headSegment" :style="{'background-color':monster.background}">
                                <img :src="getSegmentImage('head')">
                            </div>
                            <div class="row bodySegment" :style="{'background-color':monster.background}">
                                <img :src="getSegmentImage('body')">
                            </div>
                            <div class="row legsSegment" :style="{'background-color':monster.background}">
                                <img :src="getSegmentImage('legs')">
                            </div>
                        </div>
                    </div>
                </div>
                <comment-component v-if="!groupMode"
                    class="mt-3"
                    :user="user"
                    :monster-id="monster.id"
                >
                </comment-component>
                <div v-if="!user && !groupMode" class="row mt-4">
                    <div class="col-12">
                        <button class="btn btn-success btn-block" onclick="location.href='/login'">
                            Sign in to add comments
                        </button>
                    </div>
                </div>
                <div v-if="user && user.moderator==1 && createdInLastWeek" class="card border-0">
                    <div class="row">
                        <button v-if="monster.approved_by_admin == 0" :disabled="monster.suggest_rollback == 1" class="btn btn-danger btn-block mb-2" title="" @click="showRollbackConfirmation">
                            <i class="fa fa-flag"></i> Flag as inappropriate/low effort
                            <i data-toggle="tooltip" data-placement="right" title="" class="fa fa-info-circle" data-original-title="Is this monster NSFW without having a NFSW flag? Is it just a scribble? Pressing this button will hide this monster and request that it is reviewed by an admin."></i>
                        </button>
                        <div class="alert alert-success" v-if="monster.approved_by_admin == 1">
                            <i class="fa fa-check"></i>
                            Approved as acceptable by administrator. If you think it should be reviewed again send us an <a href="admin@monsterland.net">email</a>.
                        </div>
                    </div>
                    <div class="row" v-if="monster.request_take_two == 0">
                        <div class="col-sm-6 col-12 mb-1">
                            <button class="btn btn-info btn-block mb-2" title="Request new monster with same head" @click="requestTakeTwo('head')">
                                <i class="fas fa-clone"></i>  Request new monster with same head
                                <i data-toggle="tooltip" data-placement="right" title="" class="fa fa-info-circle" data-original-title="Create a new monster with the same head (leaving this monster as it is)"></i>
                            </button>
                        </div>
                        <div class="col-sm-6 col-12 mb-1">
                            <button class="btn btn-info btn-block mb-2" title="Request new monster with same head and body" @click="requestTakeTwo('body')">
                                <i class="fas fa-clone"></i>  Request new monster with same head and body
                                <i data-toggle="tooltip" data-placement="right" title="" class="fa fa-info-circle" data-original-title="Create a new monster with the same head (leaving this monster as it is)"></i>
                            </button>
                        </div>
                    </div>
                    <div class="row" v-else>
                        <div class="alert alert-info w-100">
                            <i class="fa fa-check"></i>
                            New monster request has already been submitted.
                        </div>
                    </div>
                </div>
                <div v-if="user && user.id==1 && monster.needs_validating" class="card border-0">
                    <button class="btn btn-success btn-block mb-2" title="This monster looks fine so far" @click="validate">
                        <i class="fa fa-checked"></i>  Validate latest segment looks ok
                    </button>
                </div>
                <div v-if="user && user.id==1 && monster.segments_with_images && monster.segments_with_images[0].image" class="card border-0">
                    <div class="row mt-3">
                        <div class="col-sm-4 col-12 mb-1">
                            <button class="btn btn-success btn-block mb-2" title="Take two on head" @click="takeTwo('head')">
                                <i class="fas fa-clone"></i>  Take Two (clone head)
                                <i data-toggle="tooltip" data-placement="right" title="" class="fa fa-info-circle" data-original-title="Create a new monster with the same head (leaving this monster as it is)"></i>
                            </button>
                        </div>
                        <div class="col-sm-4 col-12 mb-1">
                            <button class="btn btn-success btn-block mb-2" title="Take two on head and body" @click="takeTwo('body')">
                                <i class="fas fa-clone"></i>  Take Two (clone head & body)
                                <i data-toggle="tooltip" data-placement="right" title="" class="fa fa-info-circle" data-original-title="Create a new monster with the same head (leaving this monster as it is)"></i>
                            </button>
                        </div>
                        <div class="col-sm-4 col-12 mb-1">
                            <button class="btn btn-danger btn-block mb-2" title="Take two on head" @click="rejectTakeTwo()">
                                <i class="fas fa-times"></i>  Reject Take Two request
                                <i data-toggle="tooltip" data-placement="right" title="" class="fa fa-info-circle" data-original-title="Reject take two request"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div v-if="user && user.id==1" class="card">
                    <div class="card-body bg-warning">
                        <div class="row mt-12">
                            <div class="col-sm-12 mb-1">
                                <button class="btn btn-danger btn-block" title="It's a scribble" @click="abort">
                                    Abort the scribble!
                                </button>
                            </div>
                            <div class="col-sm-12 col-md-4 mb-1">
                                <button class="btn btn-danger btn-block" title="Not safe for work!" @click="flagNSFW">
                                    NSFW
                                </button>
                            </div>
                            <div class="col-sm-12 col-md-4 mb-1">
                                <button class="btn btn-danger btn-block" title="Not safe for life!" @click="flagNSFL">
                                    NSFL!!!
                                </button>
                            </div>
                             <div class="col-sm-12 col-md-4 mb-1">
                                <button class="btn btn-success btn-block" title="Remove NSFW and NSFL flags" @click="removeFlag">
                                    Safe
                                </button>
                            </div>

                            <div class="col-sm-12 col-md-6 mb-1">
                                <button class="btn btn-primary btn-block" title="Remove NSFW and NSFL flags" @click="rollbackLegs">
                                    Roll back Legs
                                </button>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-1">
                                <button class="btn btn-primary btn-block" title="Remove NSFW and NSFL flags" @click="rollbackBodyAndLegs">
                                    Roll back Body and Legs
                                </button>
                            </div>

                            <div class="col-sm-12 col-md-4 mb-1">
                                <button class="btn btn-primary btn-block" title="Set as Basic level" @click="updateAuthLevel('basic')">
                                    Make Basic
                                </button>
                            </div>
                            <div class="col-sm-12 col-md-4 mb-1">
                                <button class="btn btn-primary btn-block" title="Set as Standard level" @click="updateAuthLevel('standard')">
                                    Make Standard
                                </button>
                            </div>
                             <div class="col-sm-12 col-md-4 mb-1">
                                <button class="btn btn-success btn-block" title="Set as Pro level" @click="updateAuthLevel('pro')">
                                    <i class="fa fa-star"></i> Make Pro
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <flag-monster-component
            v-if="activeModal==1" 
            @close="activeModal=0"
            @flag="suggestRollback">
        </flag-monster-component>
        <div v-if="activeModal > 0" class="modal-backdrop fade show"></div>
    </div>
</template>

<script>
    import comment from './Comment';
    import flagMonsterComponent from './FlagMonster';
    export default {
        props: {
            user: Object,
            monster: Object,
            prevMonster: Object,
            nextMonster: Object,
            groupMode: {
                default: 0,
                format: Number
            },
            pageType: {
                default: 'gallery',
                format: String
            }
        },
        components : {
            comment,
            flagMonsterComponent
        },
        methods: {
            getSegmentImage: function(segment) {
                for (var i = 0; i < this.monster.segments_with_images.length; i ++){
                    if (this.monster.segments_with_images[i].segment == segment){
                        return this.monster.segments_with_images[i].image;
                    }
                }
                return '';
            },
            getCreator: function(segment_name){
                var segments = this.monster.segments;
                for (var i = 0; i < segments.length; i ++){
                    if (segments[i].segment == segment_name){
                        if (segments[i].creator){
                            return segments[i].creator;
                        }
                    }
                }
                return {
                    'id':0,
                    'name':'GUEST'
                };
            },
            getCreatorGroupUserName: function(segment_name){
                var segments = this.monster.segments;
                for (var i = 0; i < segments.length; i ++){
                    if (segments[i].segment == segment_name){
                        if (segments[i].created_by_group_username){
                            return segments[i].created_by_group_username;
                        }
                    }
                }
                return false;
            },
            userIsCreator: function(){
                var segments = this.monster.segments;
                for (var i = 0; i < segments.length; i ++){
                    if (segments[i].creator && segments[i].creator.id == this.user.id){
                        return true;
                    }
                }
                return false;
            },
            saveRating: function() {
                axios.post('/saveRating',{
                    rating: this.selectedRating,
                    monster_id: this.monster.id              
                })
                .then((response) => {
                    location.reload();
                    console.log(response); 
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            prevClick: function() {
                location.href = '/' + this.pageType + '/' + this.prevMonster.id;
            },
            nextClick: function() {
                location.href = '/' + this.pageType + '/' + this.nextMonster.id;
            },
            suggestRollback: function(){
                axios.post('/suggestRollback',{
                    monster_id: this.monster.id,   
                    action: 'suggestrollback'    
                })
                .then((response) => {
                    location.reload();
                    console.log(response); 
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            rollbackLegs: function() {
                axios.post('/rollback',{
                    monster_id: this.monster.id,  
                    segments:'legs',   
                    action: 'rollback'    
                })
                .then((response) => {
                    location.reload();
                    console.log(response); 
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            rollbackBodyAndLegs: function() {
                axios.post('/rollback',{
                    monster_id: this.monster.id,   
                    segments:'body_legs',
                    action: 'rollback'      
                })
                .then((response) => {
                    location.reload();
                    console.log(response); 
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            abort: function() {
                axios.post('/abortMonster',{
                    monster_id: this.monster.id,
                    action: 'abort'        
                })
                .then((response) => {
                    location.reload();
                    console.log(response); 
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            flagNSFW: function() {
                axios.post('/flagMonster',{
                    monster_id: this.monster.id,
                    severity: 'nsfw' ,
                    action: 'flag'          
                })
                .then((response) => {
                    location.reload();
                    console.log(response); 
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            flagNSFL: function() {
                axios.post('/flagMonster',{
                    monster_id: this.monster.id,
                    severity: 'nsfl',
                    action: 'flag'           
                })
                .then((response) => {
                    location.reload();
                    console.log(response); 
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            removeFlag: function() {
                axios.post('/flagMonster',{
                    monster_id: this.monster.id,
                    severity: 'safe',
                    action: 'flag'          
                })
                .then((response) => {
                    location.reload();
                    console.log(response); 
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            validate: function() {
                axios.post('/validateMonster',{
                    monster_id: this.monster.id,
                    action: 'validate'          
                })
                .then((response) => {
                    location.reload();
                    console.log(response); 
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            takeTwo: function(segment_name) {
                axios.post('/takeTwo',{
                    monster_id: this.monster.id,
                    action: 'takeTwo',
                    segment: segment_name          
                })
                .then((response) => {
                    location.href='/home';
                    console.log(response); 
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            rejectTakeTwo: function(){
                axios.post('/rejectTakeTwo',{
                    monster_id: this.monster.id,
                    action: 'rejectTakeTwo'   
                })
                .then((response) => {
                    location.href='/home';
                    console.log(response); 
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            requestTakeTwo: function(segment_name) {
                axios.post('/requestTakeTwo',{
                    monster_id: this.monster.id,
                    action: 'requestTakeTwo',
                    segment: segment_name          
                })
                .then((response) => {
                    location.reload();
                    console.log(response); 
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            updateAuthLevel: function(level){
                axios.post('/requestTakeTwo',{
                    monster_id: this.monster.id,
                    action: 'updateAuthLevel',
                    level: level          
                })
                .then((response) => {
                    location.href='/home';
                    console.log(response); 
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            showRollbackConfirmation: function() {
                this.activeModal = 1;
            }
        },
        computed: {
            lockPrev: function(){
                return this.prevMonster.id==this.monster.id;
            },
            lockNext: function(){
                return this.nextMonster.id==this.monster.id;
            },
            myRating: function() {
                var ratings = this.monster.ratings;
                for (var i = 0; i < ratings.length; i++){
                    if (ratings[i].user_id == this.user.id){
                        return ratings[i].rating;
                    }
                }
                return 0;
            },
            overallRating: function(){
                var ratings = this.monster.ratings;
                var totalRatings=0;
                if (ratings.length == 0) return 0;
                for (var i = 0; i < ratings.length; i++){
                     totalRatings += ratings[i].rating;
                }
                return (totalRatings/ratings.length).toFixed(2);
                
            },
            voteCount: function(){
                var votes = this.monster.ratings.length;
                var resp;
                if (votes == 0){
                    resp = 'no votes';
                }
                else if (votes == 1){
                    resp = 'from 1 vote';
                } else {
                    resp = 'from ' + votes +' votes';
                }
                return resp;
            },
            createdInLastWeek(){
                var d1 = new Date(this.monster.created_at);
                var d2 = new Date();
                d2.setDate(d2.getDate()-7);
                return  d1.getTime() >= d2.getTime();
            }
        },
        data() {
            return {
               selectedRating: 5,
               activeModal: 0,
            }
        },
        mounted() {
            console.log('Component mounted.');
            $(function () {
                $('[data-toggle="tooltip"]').tooltip()
            })
        }
    }
</script>

<style scoped>
    .monster-header{
        text-align:center;
    }
    .bodySegment, .legsSegment {
        margin-top: -33px;
    }

    .ratingRow{
        border: 2px solid red;
        background-color: pink;
        align-items: center;
        padding:2px;
        margin-bottom:8px;
    }
    .slidecontainer{
        min-height: 18px;
    }

    h5{
        font-size: 1.3rem;
    }
    .redTitle{
        background-color: #DC143C;
        color:white;
    }

    #canvas_container.closed{
        opacity:0.4;
    }

    #canvas_container{
        width:100%;
    }

    #canvas_container.useImage{
        position:relative;
    }
    #canvas_container.useImage:after {
        content: "";
        display: block;
        padding-bottom: 100%; /* The padding depends on the width, not on the height, so with a padding-bottom of 100% you will get a square */
    }
    #canvas_container.useImage img {
        width: calc(100% - 40px);
        height: calc(100% - 40px);
        object-fit: cover;
        display:block;
        object-position: center;
        position:absolute;
    }

    .monster-header.closed{
        color:grey;
    }

    /*@media only screen and (max-width: 1024px) {
        #canvas_container{
            transform:scaleX(0.78) scaleY(0.78);
            transform-origin:top left;
            height: 780px;
        }
    }

    @media only screen and (max-width: 900px) {
        #canvas_container{
            transform:scaleX(0.55) scaleY(0.55);
            transform-origin:top left;
            height: 500px;
        }
    }*/

    @media only screen and (min-width: 801px) {
        .ratingContainer{
            text-align:right;
        }
        .votesContainer{
            text-align:left;
        }
    }

    @media only screen and (max-width: 800px) {
        #canvas_container:not(.useImage){
            transform:scaleX(0.48) scaleY(0.48);
            transform-origin:top left;
            height: 480px;
        }
        h5{
            font-size: 1.0rem;
        }
    }

    @media only screen and (max-width: 600px) {
        #canvas_container:not(.useImage){
            transform:scaleX(0.44) scaleY(0.44);
            transform-origin:top left;
            height: 440px;
        }
        h5{
            font-size: 1.0rem;
        }
     }


    @media only screen and (max-width: 500px) {
        #canvas_container:not(.useImage){
            transform:scaleX(0.4) scaleY(0.4);
            transform-origin:top left;
            height: 400px;
        }
        h5{
            font-size: 1.0rem;
        }
     }

     @media only screen and (max-width: 450px) {
        #canvas_container:not(.useImage){
            transform:scaleX(0.33) scaleY(0.33);
            transform-origin:top left;
            height: 330px;
        }
        .btnLabel{
            display:none;
        }
        h5{
            font-size: 0.8rem;
        }
     }

     @media only screen and (max-width: 400px) {
        #canvas_container:not(.useImage){
            transform:scaleX(0.28) scaleY(0.28);
            transform-origin:top left;
            height: 280px;
        }
        .btnLabel{
            display:none;
        }
        h5{
            font-size: 0.8rem;
        }
     }

    @media only screen and (max-width: 350px) {
        #canvas_container:not(.useImage){
            transform:scaleX(0.23) scaleY(0.23);
            transform-origin:top left;
            height: 230px;
        }
        .btnLabel{
            display:none;
        }
        h5{
            font-size: 0.5rem;
        }
     }

</style>
