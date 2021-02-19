 <template>
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
            <div id="favouriteIcon" @click="favouriteIconClicked" v-if="showFavouriteIcon">
                <i v-if="isFavourite" class="fa fa-heart heart filled" title="Favourite"></i>
                <i v-else class="far fa-heart heart outline" title="Add as favourite"></i>
            </div>
            <div class="col-12 pl-2 pr-2">
                <h1 id="monsterTitle">
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
                    <button class="btn btn-success btn-sm btn-block text-nowrap" @click="saveRating">
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
</template>

<script>
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
        methods: {
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
                    // location.reload();
                    // this.$emit('headerChanged');
                    var newRating = [];
                    newRating.user_id = this.user.id;
                    newRating.monster_id = this.monster.id;
                    newRating.rating = parseInt(this.selectedRating);
                    this.currentRatings.push(newRating);
                    console.log(response); 
                })
                .catch((error) => {
                    console.log(error);
                });
                
            },
            prevClick: function() {
                this.$emit('prevClicked');
            },
            nextClick: function() {
                this.$emit('nextClicked');
            },
            copyUrl(linkType, url) {
                const el = document.createElement('textarea');  
                el.value = url;                                 
                el.setAttribute('readonly', '');                
                el.style.position = 'absolute';                     
                el.style.left = '-9999px';                      
                document.body.appendChild(el);                  
                const selected =  document.getSelection().rangeCount > 0  ? document.getSelection().getRangeAt(0) : false;                                    
                el.select();                                    
                document.execCommand('copy');                   
                document.body.removeChild(el);                  
                if (selected) {                                 
                    document.getSelection().removeAllRanges();    
                    document.getSelection().addRange(selected);   
                }
                switch (linkType){
                    case 'permanentLink':
                        this.permanentLinkCopied=true;
                    break;
                    case 'imageURL':
                        this.imageURLCopied=true;
                    break;
                }
            },
            commentAdded: function(){
                this.componentKey += 1;  
            },
            startTshirtOrder: function(){
                // axios.get('/tshirt/build/' + this.monster.id)
                // .then((response) => {
                //     console.log(response); 
                // })
                // .catch((error) => {
                //     console.log(error);
                // });
                location.href='/tshirt/build/' + this.monster.id;
            },
            removeFavourite: function(){
                axios.post('/removeFavourite',{
                    monster_id: this.monster.id,
                    action: 'removeFavourite'     
                })
                .then((response) => {
                    // location.reload();
                    var users = this.currentlyFavouritedByUsers;
                    for(var i=0; i < users.length; i++){
                        if (users[i].id == this.user.id) {
                            //users[i].remove();
                            users.splice(i, 1);
                        }
                    }
                    //this.$emit('headerChanged');
                    console.log(response); 
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            favouriteIconClicked: function(){
                if (this.isFavourite){
                    this.removeFavourite();
                } else{
                    this.addFavourite();
                }
            },
            addFavourite: function(){
                axios.post('/addFavourite',{
                    monster_id: this.monster.id,
                    action: 'addFavourite'     
                })
                .then((response) => {
                    // location.reload();
                    this.currentlyFavouritedByUsers.push(this.user);
                    //this.$emit('headerChanged'); 
                    console.log(response); 
                })
                .catch((error) => {
                    console.log(error);
                });
            }
        },
        computed: {
            lockPrev: function(){
                if (this.pageType == 'gallery'){
                    return this.prevMonster.id==this.monster.id;
                } else {
                    return this.skip < 1 ;
                }
            },
            lockNext: function(){
                if (this.pageType == 'gallery'){
                    return this.nextMonster.id==this.monster.id;
                } else {
                    return this.skip >= (this.monsterCount-1);
                }
            },
            myRating: function() {
                var ratings = this.currentRatings;
                for (var i = 0; i < ratings.length; i++){
                    if (ratings[i].user_id == this.user.id){
                        return ratings[i].rating;
                    }
                }
                return 0;
            },
            overallRating: function(){
                var ratings = this.currentRatings;
                var totalRatings=0;
                if (ratings.length == 0) return 0;
                for (var i = 0; i < ratings.length; i++){
                     totalRatings += ratings[i].rating;
                }
                return (totalRatings/ratings.length).toFixed(2);
                
            },
            voteCount: function(){
                var votes = this.currentRatings.length;
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
            // createdInLastWeek(){
            //     var d1 = new Date(this.monster.created_at);
            //     var d2 = new Date();
            //     d2.setDate(d2.getDate()-7);
            //     return  d1.getTime() >= d2.getTime();
            // }
            isFavourite() {
                if (this.user){
                    for(var i = 0; i < this.currentlyFavouritedByUsers.length; i++){
                        if (this.currentlyFavouritedByUsers[i].id == this.user.id) {
                            return true;
                        }                    
                    }
                }
                return false;
            },
            showFavouriteIcon(){
                return this.user && (this.userIsCreator() || this.myRating > 0)
            }
        },
        data() {
            return {
                selectedRating: 5,
                activeModal: 0,
                permanentLinkCopied: false,
                imageURLCopied: false,
                currentTakeTwoCount: this.user ? this.user.take_two_count : 0,
                componentKey: 0,
                currentlyFavouritedByUsers: this.monster.favourited_by_users,
                currentRatings: this.monster.ratings
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
        border-radius:4px;
        background-color: pink;
        align-items: center;
        padding:4px;
        margin-bottom:8px;
        color:black;
        font-weight:600;
        font-size: 1.1rem;
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

    .monster-header.closed{
        color:grey;
    }

    .border-0{
        border:none!important;
    }
    .heart.filled{
        color:lightpink;
        font-size:41px;
    }
    .heart{
        cursor:pointer;
    }
    .heart.outline:hover{
        color:lightpink;
    }
    .heart.outline{
        font-size:30px;
        opacity:0.5;
        padding-left:5px;
        padding-right:5px;
    }
    #favouriteIcon{
        position:absolute;
        z-index:100;
    }

    @media only screen and (min-width: 768px) {
        .ratingContainer{
            text-align:right;
        }
        .votesContainer{
            text-align:left;
        }
    }

    @media only screen and (max-width: 800px) {
        h1{
            font-size: 1.5rem;
        }
        h4{
            font-size: 1rem;
        }
        h5{
            font-size: 1.0rem;
        }
        .heart.outline{
            padding: 0 !important;
        }
    }

    @media only screen and (max-width: 600px) {
        h1{
            font-size: 1.3rem;
        }
        h4{
            font-size: 1rem;
        }
        h5{
            font-size: 1.0rem;
        }
        .heart.outline{
            padding: 0 !important;
        }
     }


    @media only screen and (max-width: 500px) {
        h4{
            font-size: 1rem;
        }
        h5{
            font-size: 1.0rem;
        }
        .heart.outline{
            padding: 0 !important;
        }
     }

     @media only screen and (max-width: 450px) {
        .btnLabel{
            display:none;
        }
        h5{
            font-size: 0.8rem;
        }
        .heart.outline{
            padding: 0 !important;
        }
     }

     @media only screen and (max-width: 400px) {
        .btnLabel{
            display:none;
        }
        h1{
            font-size: 1.1rem;
        }
        h4{
            font-size: 0.8rem;
        }
        h5{
            font-size: 0.8rem;
        }
        #monsterTitle{
            padding-left:20px;
            padding-right:20px;
        }
        .heart{
            font-size:20px!important;
        }
        .heart.outline{
            padding: 0 !important;
        }
     }

    @media only screen and (max-width: 350px) {
        .btnLabel{
            display:none;
        }
        h1{
            font-size: 0.9rem;
        }
        h4{
            font-size: 0.7rem;
        }
        h5{
            font-size: 0.5rem;
        }
        #monsterTitle{
            padding-left:20px;
            padding-right:20px;
        }
        .heart{
            font-size:20px!important;
        }
        .heart.outline{
            padding: 0 !important;
        }
     }

</style>