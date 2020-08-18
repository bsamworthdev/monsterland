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
                            <div class="row mt-3" :class="{'redTitle':monster.nsfl||monster.nsfw}">
                                <div class="col-12">
                                    <h1>
                                        {{ monster.name }}
                                        <span v-if="monster.nsfl">(NSFL)</span>
                                        <span v-else-if="monster.nsfw">(NSFW)</span>
                                    </h1>
                                </div>
                            </div>

                            <div v-if="!user" class="row">
                                <div class="col-6 text-right">
                                    <h4>Overall Rating {{ overallRating }}</h4>
                                </div>
                                <div class="col-6 text-left">
                                    <button class="btn btn-success" onclick="location.href='/register'">
                                        Sign up to vote
                                    </button>
                                </div>
                            </div>
                            <div v-else-if="userIsCreator()" class="row">
                                <div class="col-12">
                                    <h4>Overall Rating {{ overallRating }}</h4>
                                </div>
                            </div>
                            <div v-else-if="myRating > 0" class="row">
                                <div class="col-6 text-right">
                                    <h4>Overall Rating {{ overallRating }}</h4>
                                </div>
                                <div class="col-6 text-left">
                                    <h4>(Your Rating {{ myRating }})</h4>
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
                            <div class="row mt-1">
                                <div class="col-4">
                                    <h5>Head: 
                                        <a v-if="getCreator('head').id != 0" 
                                        :href="'/monsters/' + getCreator('head').id ">
                                            <b>{{ getCreator('head').name }}</b>
                                        </a>
                                        <b v-else>GUEST</b>
                                    </h5>
                                </div>
                                <div class="col-4 ">
                                    <h5>Body:
                                        <a v-if="getCreator('body').id != 0" 
                                        :href="'/monsters/' + getCreator('body').id ">
                                            <b>{{ getCreator('body').name }}</b>
                                        </a>
                                        <b v-else>GUEST</b>
                                    </h5>
                                </div>
                                <div class="col-4">
                                    <h5>Legs: 
                                        <a v-if="getCreator('legs').id != 0" 
                                        :href="'/monsters/' + getCreator('legs').id ">
                                            <b>{{ getCreator('legs').name }}</b>
                                        </a>
                                        <b v-else>GUEST</b>
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="canvas_container" :class="{'closed':monster.status=='cancelled'}" class="card-body">
                        <div v-if="monster.image && monster.image != 'n/a'" class="container">
                            <div class="row">
                                <img :src="monster.image">
                            </div>
                        </div>
                        <div v-else class="container">
                             <div class="row headSegment">
                                <img :src="getSegmentImage('head')">
                            </div>
                            <div class="row bodySegment">
                                <img :src="getSegmentImage('body')">
                            </div>
                            <div class="row legsSegment">
                                <img :src="getSegmentImage('legs')">
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="!user" class="row mt-4">
                    <div class="col-12">
                        <button class="btn btn-success btn-block" onclick="location.href='/register'">
                            Create Account
                        </button>
                    </div>
                </div>
                <comment-component
                    v-if="user"
                    class="mt-3"
                    :user="user"
                    :monster-id="monster.id"
                >
                </comment-component>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import comment from './Comment';
    export default {
        props: {
            user: Object,
            monster: Object,
            prevMonster: Object,
            nextMonster: Object
        },
        components : {
            comment
        },
        methods: {
            getSegmentImage: function(segment) {
                for (var i = 0; i < this.monster.segments.length; i ++){
                    if (this.monster.segments[i].segment == segment){
                        return this.monster.segments[i].image;
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
                location.href = '/gallery/' + this.prevMonster.id;
            },
            nextClick: function() {
                location.href = '/gallery/' + this.nextMonster.id;
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
                
            }
        },
        data() {
            return {
               selectedRating: 5
            }
        },
        mounted() {
            console.log('Component mounted.')
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

    #canvas_container img {
        width:100%;
        object-fit: cover;
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

    @media only screen and (max-width: 800px) {
        /*#canvas_container{
            transform:scaleX(0.48) scaleY(0.48);
            transform-origin:top left;
            height: 480px;
        }*/
        h5{
            font-size: 1.0rem;
        }
    }

    @media only screen and (max-width: 600px) {
        /*#canvas_container{
            transform:scaleX(0.44) scaleY(0.44);
            transform-origin:top left;
            height: 440px;
        }*/
        h5{
            font-size: 1.0rem;
        }
     }


    @media only screen and (max-width: 500px) {
        /*#canvas_container{
            transform:scaleX(0.4) scaleY(0.4);
            transform-origin:top left;
            height: 400px;
        }*/
        h5{
            font-size: 1.0rem;
        }
     }

     @media only screen and (max-width: 450px) {
        /*#canvas_container{
            transform:scaleX(0.33) scaleY(0.33);
            transform-origin:top left;
            height: 330px;
        }*/
        .btnLabel{
            display:none;
        }
        h5{
            font-size: 0.8rem;
        }
     }

     @media only screen and (max-width: 400px) {
        /*#canvas_container{
            transform:scaleX(0.28) scaleY(0.28);
            transform-origin:top left;
            height: 280px;
        }*/
        .btnLabel{
            display:none;
        }
        h5{
            font-size: 0.8rem;
        }
     }

    @media only screen and (max-width: 350px) {
        /*#canvas_container{
            transform:scaleX(0.23) scaleY(0.23);
            transform-origin:top left;
            height: 230px;
        }*/
        .btnLabel{
            display:none;
        }
        h5{
            font-size: 0.5rem;
        }
     }

</style>
