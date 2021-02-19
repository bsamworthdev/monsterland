<template>
    <div class="container">
        <div class="row mb-2">
            <div class="col-lg-3 col-4 mt-1">
                <h3 class="text-right">
                    Top rated
                </h3> 
            </div>
            <div class="col-lg-3 col-8 mt-1">
                <select id="timeFilter" v-model="selectedTimeFilter" class="form-control" @change="timeFilterChanged($event)">
                    <option value="day">Day</option>
                    <option value="week">Week</option>
                    <option value="month">Month</option>
                    <option value="year">Year</option> 
                    <option value="ever">All Time</option> 
                </select>
            </div>
            <div class="col-lg-2 col-4 mt-1">
                <h5 class="text-right">
                    Search
                </h5> 
            </div>
            <div class="col-lg-3 col-6 mt-1">
                <input id="searchText" class="form-control" type="text" v-model="enteredSearchText" @keydown="searchKeyDown" />
            </div>
            <div class="col-lg-1 col-2 mt-1 pull-left pl-0">
                <button class="btn btn-success btn-block pl-0 pr-0" :disabled="lockSearch" @click="searchClick">
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <gallery-header-component
                            :user="user"
                            :monster="monster"
                            :prev-monster="prevMonster"
                            :next-monster="nextMonster"
                            :group-mode="groupMode"
                            :page-type="pageType"
                            :key="headerComponentKey"
                            @headerChanged="headerChanged"
                            @prevClicked="prevClicked"
                            @nextClicked="nextClicked"
                        >
                       </gallery-header-component>
                    </div>
                    <div id="canvas_container" :class="{'closed':monster.status=='cancelled','useImage':monster.image && monster.image != 'n/a'}" class="card-body">
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
                    <div class="card-footer">
                        <div class="container">
                            <div class="row">
                                <div class="col-12" :class="{'copied':permanentLinkCopied }" id="permanentLinkCopied">
                                    Permanent link: <a :href="'https://monsterland.net/gallery/' + monster.id">monsterland.net/gallery/{{ monster.id }}</a>
                                    <i class="fa fa-copy pl-1" title="copy link" @click="copyUrl('permanentLink','https://monsterland.net/gallery/' + monster.id)"></i>
                                    <div class="copyMessage text text-success col-md-4 col-md-offset-4">Copied!</div>
                                </div>
                                <div class="col-12" :class="{'copied':imageURLCopied }" id="imageURLCopied">
                                    Image URL (for hotlinking/embedding): <a :href="'https://monsterland.net/storage/' + monster.id + '.png'">monsterland.net/storage/{{ monster.id }}.png</a>
                                    <i class="fa fa-copy pl-1" title="copy link" @click="copyUrl('imageURL','https://monsterland.net/storage/' + monster.id + '.png')"></i>
                                    <div class="copyMessage text text-success col-md-4 col-md-offset-4">Copied!</div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-6">
                                    <button class="btn btn-info btn-block" :disabled="lockPrev" @click="prevClicked">
                                        <i class="fas fa-arrow-left"></i> <span class="btnLabel">Previous</span>
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button class="btn btn-info btn-block" :disabled="lockNext" @click="nextClicked">
                                        <span class="btnLabel">Next</span> <i class="fas fa-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <comment-component v-if="!groupMode"
                    class="mt-3"
                    :user="user"
                    :monster-id="monster.id"
                    :key="commentComponentKey"
                    @commentAdded="commentAdded"
                >
                </comment-component>
                <div v-if="!user && !groupMode" class="row mt-4">
                    <div class="col-12">
                        <button class="btn btn-success btn-block" onclick="location.href='/login'">
                            Sign in to add comments
                        </button>
                    </div>
                </div>
                 <div v-if="user && user.id==1 && monster.needs_validating">
                    <div class="col-sm-12 mb-1">
                        <button class="btn btn-success btn-block" title="This monster looks fine so far" @click="validate">
                            Validate latest segment
                        </button>
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
                            <div v-if="user.id==1" class="row mt-3">
                                <button class="btn btn-success btn-block m-2" title="Get this on a T-shirt" @click="startTshirtOrder">
                                    Get this on a T-shirt!!!
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
    import commentComponent from './Comment';
    import galleryHeaderComponent from './GalleryHeader';
    export default {
        props: {
            user: {
                default: null,
                format: Object
            },
            monster: Object,
            monsterCount: Number,
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
            skip: Number
        },
        components : {
            commentComponent,
            galleryHeaderComponent
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
            prevClicked: function() {
                location.href = '/halloffamesingle/' + (this.skip-1) + '/' + this.selectedTimeFilter + (this.enteredSearchText ? '/' + this.enteredSearchText : '');
            },
            nextClicked: function() {
                location.href = '/halloffamesingle/' + (this.skip+1) + '/' + this.selectedTimeFilter + (this.enteredSearchText ? '/' + this.enteredSearchText : '');
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
            timeFilterChanged: function(event) {
                location.href = '/halloffame/0/' + event.target.value + '/' + this.enteredSearchText;
            },
            searchClick: function(event) {
                location.href = '/halloffame/0/' + this.timeFilter + '/' + this.enteredSearchText;
            },
            searchKeyDown: function(event) { 
                if (event.keyCode === 13) { //enter
                    location.href = '/halloffame/0/' + this.timeFilter + '/' + this.enteredSearchText;
                }
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
                this.commentComponentKey += 1;  
            },
            startTshirtOrder: function(){
                location.href='/tshirt/build/' + this.monster.id;
            },
            headerChanged: function(){
                this.headerComponentKey += 1;
            }, 
        },
        computed: {
            lockSearch: function(){
                return this.enteredSearchText.length == 0;
            },
            myRating: function() {
                if (this.user){
                    var ratings = this.monster.ratings;
                    for (var i = 0; i < ratings.length; i++){
                        if (ratings[i].user_id == this.user.id){
                            return ratings[i].rating;
                        }
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
            
        },
        data() {
            return {
                selectedTimeFilter : this.timeFilter,
                enteredSearchText : this.search,
                selectedRating: 5,
                permanentLinkCopied: false,
                imageURLCopied: false,
                commentComponentKey: 0,
                headerComponentKey: 0
            }
        },
        mounted() {
            this.selectedTimeFilter = this.timeFilter;
            this.enteredSearchText = this.search;
            console.log('Component mounted.')
        }
    }
</script>

<style scoped>
     .bodySegment, .legsSegment {
        margin-top: -33px;
    }
    .slidecontainer{
        min-height: 18px;
    }
    h5{
        font-size: 1.3rem;
    }

    #canvas_container.closed{
        opacity:0.4;
    }

    #canvas_container{
        width:100%;
        background-color:#FFF;
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

    .fa-copy{
        cursor:pointer;
    }

    .appLink{
        color:blue;
    }
    .border-0{
        border:none!important;
    }

    #permanentLinkCopied .copyMessage, 
    #imageURLCopied .copyMessage{
        display:none;
    }

    #permanentLinkCopied.copied .copyMessage, 
    #imageURLCopied.copied .copyMessage{
        display:inline!important;
    }

    @media only screen and (max-width: 800px) {
        #canvas_container:not(.useImage){
            transform:scaleX(0.48) scaleY(0.48);
            transform-origin:top left;
            height: 480px;
        }
        h1{
            font-size: 1.5rem;
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
        h1{
            font-size: 1.3rem;
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
