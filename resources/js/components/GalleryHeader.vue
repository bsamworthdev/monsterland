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
                

                <span v-if="isFavourite" class="fa-stack filled pl-1" title="Favourite">
                    <span class="heart fa fa-heart fa-stack-2x"></span>
                    <strong class="fa-stack-1x">
                        <span class="favouriteCount"> 
                            {{ currentlyFavouritedByUsers.length }}
                        </span>
                    </strong>
                </span>
                    <span v-else class="fa-stack outline pl-1" title="Add to favourites">
                    <span class="heart far fa-heart fa-stack-2x"></span>
                    <strong class="fa-stack-1x">
                        <span class="favouriteCount"> 
                            {{ currentlyFavouritedByUsers.length }}
                        </span>
                    </strong>
                </span>
            </div>
            <div class="col-12 pl-2 pr-2">
                <h1 id="monsterTitle" class="m-0">
                    {{ monster.name }}
                    <span v-if="monster.nsfl">(NSFL)</span>
                    <span v-else-if="monster.nsfw">(NSFW)</span>
                    <span v-else-if="monster.suggest_rollback">(FLAGGED)</span>
                </h1>
                <p class="p-0 m-0 mb-1"><i>Created {{ monster.completed_at_tidy }}</i></p>
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
                    <p class="mb-2">
                        (You rated this {{ myRating }}
                        <button id="cancelRating" @click="cancelRating" class="btn btn-link p-0">
                            <i class="fa fa-pen"></i>
                        </button>)
                    </p>
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
                    <a v-if="headCreator.id != 0" 
                    :href="'/monstergrid/usermonsters/' + headCreator.id ">
                        <b>{{ headCreator.name }} <i title="pro user" v-if="headCreator.vip" class="fa fa-star"></i></b>
                    </a>
                    <b v-else-if="getCreatorGroupUserName('head')">{{ getCreatorGroupUserName('head') }}</b>
                    <b v-else>GUEST</b>
                </h5>
                <div class="accountsContainer" v-if="headCreator.social_media_accounts">
                    <div v-for="(account, index) in headCreator.social_media_accounts" :key="index" class="account pt-1">
                        <a :href="getUrl(account)" :title="getUrl(account)" target="_blank">
                            <img class="icon" :src="'/images/' + account.account_type + '.png'">
                            <span v-show="editMode">
                                {{ account.account_name }}
                            </span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-4 ">
                <h5>Body:
                    <a v-if="bodyCreator.id != 0" 
                    :href="'/monstergrid/usermonsters/' + bodyCreator.id ">
                        <b>{{ bodyCreator.name }} <i title="pro user" v-if="bodyCreator.vip" class="fa fa-star"></i></b>
                    </a>
                    <b v-else-if="getCreatorGroupUserName('body')">{{ getCreatorGroupUserName('body') }}</b>
                    <b v-else>GUEST</b>
                    <i class="fas fa-eye ml-1 text-success" v-if=" user && user.peek_view && getSegment('body').peekUsed" title="peeked"></i>
                </h5>
                <div class="accountsContainer" v-if="bodyCreator.social_media_accounts">
                    <div v-for="(account, index) in bodyCreator.social_media_accounts" :key="index" class="account pt-1">
                        <a :href="getUrl(account)" :title="getUrl(account)" target="_blank">
                            <img class="icon" :src="'/images/' + account.account_type + '.png'">
                            <span v-show="editMode">
                                {{ account.account_name }}
                            </span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <h5>Legs: 
                    <a v-if="legsCreator.id != 0" 
                    :href="'/monstergrid/usermonsters/' + legsCreator.id ">
                        <b>{{ legsCreator.name }} <i title="pro user" v-if="legsCreator.vip" class="fa fa-star"></i></b>
                    </a>
                    <b v-else-if="getCreatorGroupUserName('legs')">{{ getCreatorGroupUserName('legs') }}</b>
                    <b v-else>GUEST</b>
                    <i class="fas fa-eye ml-1 text-success" v-if="user && user.peek_view && getSegment('legs').peekUsed" title="peeked"></i>
                </h5>
                <div class="accountsContainer" v-if="legsCreator.social_media_accounts">
                    <div v-for="(account, index) in legsCreator.social_media_accounts" :key="index" class="account pt-1">
                        <a :href="getUrl(account)" :title="getUrl(account)" target="_blank">
                            <img class="icon" :src="'/images/' + account.account_type + '.png'">
                            <span v-show="editMode">
                                {{ account.account_name }}
                            </span>
                        </a>
                    </div>
                </div>
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
            },
            search: String,
            timeFilter: String,
            skip:{
                default: null,
                format: Number
            },
            monsterCount:{
                default: null,
                format: Number
            }
        },
        methods: {
            getUrl: function(account){
                switch (account.account_type) {
                    case 'facebook':
                        return 'https://facebook.com/groups/' + account.account_name;
                        break;
                    case 'instagram':
                        return 'https://instagram.com/' + account.account_name;
                        break;
                    case 'twitter':
                        return 'https://twitter.com/' + account.account_name;
                        break;
                    case 'twitch':
                        return 'https://twitch.com/' + account.account_name;
                        break;
                    case 'discord':
                        return 'https://discord.com/' + account.account_name;
                        break;
                    case 'youtube':
                        return 'https://youtube.com/' + account.account_name;
                        break;
                    case 'reddit':
                        return 'https://reddit.com/u/' + account.account_name;
                        break;
                }
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
            getSegment: function(segment_name){
                var segments = this.monster.segments;
                for (var i = 0; i < segments.length; i ++){
                    if (segments[i].segment == segment_name){
                        return segments[i];
                    }
                };
                return [];
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
            cancelRating: function() {
                axios.post('/cancelRating',{
                    monster_id: this.monster.id              
                })
                .then((response) => {
                    for (var i = 0; i < this.currentRatings.length; i ++){ 
                        if (this.currentRatings[i].monster_id == this.monster.id &&
                            this.currentRatings[i].user_id == this.user.id){
                            this.currentRatings.splice(i, 1);
                        }
                    }

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
            },
            setUpSegmentCreators: function(){
                this.headCreator = this.getCreator('head');
                this.bodyCreator = this.getCreator('body');
                this.legsCreator = this.getCreator('legs');
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
                currentRatings: this.monster.ratings,
                headCreator: null,
                bodyCreator: null,
                legsCreator: null

            }
        },
        mounted() {
            console.log('Component mounted.');
            $(function () {
                $('[data-toggle="tooltip"]').tooltip()
            })
            
        },
        beforeMount() {
            this.setUpSegmentCreators();
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
    .fa-stack{
        font-size:8px;
        height:2.3em!important;
    }
    .fa-stack.outline{
        font-size:18px;
    }
    .fa-stack.filled{
        font-size:18px;
    }
    .favouriteCount{
        color:#000;
        font-size:16px;
    }
    .filled .heart{
        color:lightpink;
    }
    .heart{ 
        cursor:pointer;
    }
    .outline .heart:hover{
        color:lightpink;
    }
    .outline .heart{
        opacity:0.5;
    }
    .outline .favouriteCount{
        color:#FFF;
    }
    #favouriteIcon{
        position:absolute;
        z-index:100;
        cursor:pointer;
    }
    #cancelRating{
        font-size:12px;
        margin-bottom:3px;
        margin-right:1px;
    }
   .icon{
       height:20px;
       width:20px;
   }
   .account{
       padding:2px;
       display:inline;
   }
    @media only screen and (min-width: 768px) {
        .ratingContainer{
            text-align:right;
        }
        .votesContainer{
            width:30px;
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
        .fa-stack{
            font-size:12px!important;
        }
        .favouriteCount{
            font-size:11px!important;
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
     }


    @media only screen and (max-width: 500px) {
        h4{
            font-size: 1rem;
        }
        h5{
            font-size: 1.0rem;
        }
     }

     @media only screen and (max-width: 450px) {
        .btnLabel{
            display:none;
        }
        h5{
            font-size: 0.8rem;
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
     }

</style>