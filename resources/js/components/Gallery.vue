<template>
    <div class="container">
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
                    <div class="card-footer">
                        <div class="container">
                            <div class="row mt-1 mb-0">
                                <div class="col-12">
                                    <label class="float-left mr-2">Tags:</label>
                                    <label v-if="currentMonsterTags.length == 0" class="float-left"><i>none</i></label>
                                    <div v-for="tag in currentMonsterTags" :key="tag.id" @click="searchByTag(tag.id)" class="alert alert-info monsterTag pt-0 pb-0 mr-1 float-left">
                                        #{{ tag.name }}
                                        <a v-show="editTagMode" @click.stop.prevent="removeTag(tag.id)">
                                            <i class="fa fa-times text-danger"></i>
                                        </a>
                                    </div>
                                    <div v-if="user && user.moderator == 1" class="d-inline float-left">
                                        <a class="btn btn-link pt-0" @click="addTag"> 
                                            <i class="fa fa-plus"></i>
                                            add 
                                        </a>
                                        <a class="btn btn-link pt-0" @click="toggleEditTagMode"> 
                                            <i v-if="!editTagMode" class="fas fa-pencil-alt"></i>
                                            <i v-else class="fa fa-times"></i>
                                            {{ editTagMode ? 'stop editing' : 'edit' }}
                                        </a>
                                    </div>
                                </div>
                            </div>
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
                            <div v-if="everyoneCanUseStore == '1' || (user && user.canUseStore)" class="row mt-3">
                                <button class="btn btn-lg btn-success btn-block m-2" title="Get this monster on a T-shirt" @click="startTshirtOrder">
                                    Get this on a T-shirt!!!
                                    <i class="fas fa-tshirt pl-2"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <comment-component v-if="!groupMode"
                    class="mt-3"
                    :user="user"
                    :monster-id="monster.id"
                    :key="commentComponentKey"
                    :characters="characters"
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
                <div v-if="user" class="card border-0">
                    <div class="container">
                        <div class="row" v-if="user.moderator==1">
                            <button v-if="monster.approved_by_admin == 0" :disabled="monster.suggest_rollback == 1" class="btn btn-danger btn-block m-2" title="" @click="showRollbackConfirmation">
                                <i class="fa fa-flag"></i> Flag as inappropriate/low effort
                                <i data-toggle="tooltip" data-placement="right" title="" class="fa fa-info-circle" data-original-title="Is this monster NSFW without having a NFSW flag? Is it just a scribble? Pressing this button will hide this monster and request that it is reviewed by an admin."></i>
                            </button>
                            <div class="alert alert-success m-2" v-if="monster.approved_by_admin == 1">
                                <i class="fa fa-check"></i>
                                Approved as acceptable by administrator. If you think it should be reviewed again send us an <a href="admin@monsterland.net">email</a>.
                            </div>
                        </div>
                        <div v-if="user && user.id==1 && monster.needs_validating" class="row">
                            <button class="btn btn-success btn-block mb-2" title="This monster looks fine so far" @click="validate">
                                <i class="fa fa-checked"></i>  Validate latest segment looks ok
                            </button>
                        </div>
                        <div v-if="user && (user.vip || !monster.vip)" class="row alert alert-info mb-0">
                            <div class="col-12">
                                <h5>Create new monster with same head/body/legs: 
                                    <small v-if="user.has_used_app || user.is_patron">Unlimited</small>
                                    <small v-else :class="[{'text-danger':currentTakeTwoCount == 0},'text-wrap']">
                                        {{ currentTakeTwoCount }} remaining
                                    </small>
                                </h5>
                            </div>
                            <div class="card border-5 col-md-6 col-12 p-1 mb-2" style="z-index:1">
                                <div class="card-body p-1">
                                    <h6 class="card-title">Head First</h6>
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-sm-6 col-12 p-1">
                                                <button :disabled="(user.has_used_app==0 && user.is_patron==0 && user.take_two_count == 0)" class="btn btn-success btn-block mb-2" title="Take two on head" @click="takeTwo('head')">
                                                    <i class="fas fa-clone"></i>  Same Head Only
                                                    <i data-toggle="tooltip" data-placement="right" title="" class="fa fa-info-circle" data-original-title="Create a new monster with the same head (leaving this monster as it is)"></i>
                                                </button>
                                            </div>
                                            <div class="col-sm-6 col-12 p-1">
                                                <button :disabled="(user.has_used_app==0 && user.is_patron==0 && user.take_two_count == 0)" class="btn btn-success btn-block mb-2" title="Take two on head and body" @click="takeTwo('head_body')">
                                                    <i class="fas fa-clone"></i>  Same Head & Body
                                                    <i data-toggle="tooltip" data-placement="right" title="" class="fa fa-info-circle" data-original-title="Create a new monster with the same head and body (leaving this monster as it is)"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card border-5 col-md-6 col-12 p-1 mb-2">
                                <div class="card-body p-1">
                                    <h6 class="card-title">Legs First</h6>
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-sm-6 col-12 p-1">
                                                <button :disabled="(user.has_used_app==0 && user.is_patron==0 && user.take_two_count == 0)" class="btn btn-success btn-block mb-2" title="Take two on legs" @click="takeTwo('legs')">
                                                    <i class="fas fa-clone"></i>  Same Legs Only
                                                    <i data-toggle="tooltip" data-placement="right" title="" class="fa fa-info-circle" data-original-title="Create a new monster with the same legs (leaving this monster as it is)"></i>
                                                </button>
                                            </div>
                                            <div class="col-sm-6 col-12 p-1">
                                                <button :disabled="(user.has_used_app==0 && user.is_patron==0 && user.take_two_count == 0)" class="btn btn-success btn-block mb-2" title="Take two on body and legs" @click="takeTwo('legs_body')">
                                                    <i class="fas fa-clone"></i>  Same Body & Legs
                                                    <i data-toggle="tooltip" data-placement="right" title="" class="fa fa-info-circle" data-original-title="Create a new monster with the same legs and body (leaving this monster as it is)"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12" v-if="currentTakeTwoCount == 0">
                                <div class="alert alert-danger mt-1" v-if="currentTakeTwoCount==0 && !user.has_used_app && !user.is_patron">
                                You have no redraws left. <a href="/mobileapp">Download the app</a> or <a href="https://www.patreon.com/monsterlandgame">become a patron</a> to get unlimited redraws.
                                <br/>
                                You can also win extra redraws on the <a href="/taggame">tagging game</a>.
                            </div>
                            </div>
                        </div>
                        <div v-if="user && user.moderator==1 && user.take_two_count == 0">
                            <div class="row mt-2" v-if="monster.request_take_two == 0">
                                <div class="col-sm-3 col-12 mb-2">
                                    <button class="btn btn-info btn-block" title="Request new monster with same head" @click="requestTakeTwo('head')">
                                        <i class="fas fa-clone"></i>  Request new monster with same head
                                        <i data-toggle="tooltip" data-placement="right" title="" class="fa fa-info-circle" data-original-title="Create a new monster with the same head (leaving this monster as it is)"></i>
                                    </button>
                                </div>
                                <div class="col-sm-3 col-12 mb-2">
                                    <button class="btn btn-info btn-block" title="Request new monster with same head and body" @click="requestTakeTwo('body')">
                                        <i class="fas fa-clone"></i>  Request new monster with same head and body
                                        <i data-toggle="tooltip" data-placement="right" title="" class="fa fa-info-circle" data-original-title="Create a new monster with the same head (leaving this monster as it is)"></i>
                                    </button>
                                </div>
                                <div class="col-sm-3 col-12 mb-2">
                                    <button class="btn btn-info btn-block" title="Request new monster with same legs" @click="requestTakeTwo('legs')">
                                        <i class="fas fa-clone"></i>  Request new monster with same legs
                                        <i data-toggle="tooltip" data-placement="right" title="" class="fa fa-info-circle" data-original-title="Create a new monster with the same legs (leaving this monster as it is)"></i>
                                    </button>
                                </div>
                                <div class="col-sm-3 col-12 mb-2">
                                    <button class="btn btn-info btn-block" title="Request new monster with same body and legs" @click="requestTakeTwo('body')">
                                        <i class="fas fa-clone"></i>  Request new monster with same body and legs
                                        <i data-toggle="tooltip" data-placement="right" title="" class="fa fa-info-circle" data-original-title="Create a new monster with the same body and legs (leaving this monster as it is)"></i>
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
                        <div class="row" v-if="user && user.id==1" >
                            <div class=" col-12 mb-1">
                                <button class="btn btn-danger btn-block mb-2" title="Take two on head" @click="rejectTakeTwo()">
                                    <i class="fas fa-times"></i> Reject Take Two request
                                    <i data-toggle="tooltip" data-placement="right" title="" class="fa fa-info-circle" data-original-title="Reject take two request"></i>
                                </button>
                            </div>
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
                        </div>
                    
                        <h5 class="mb-0 mt-3">Head First Monsters (normal)</h5>
                        <div class="row">
                            <div class="col-sm-12 col-md-6 mb-1">
                                <button class="btn btn-primary btn-block" title="Roll back legs" @click="rollback('legs')">
                                    Roll back Legs
                                </button>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-1">
                                <button class="btn btn-primary btn-block" title="Roll back body and legs" @click="rollback('body_legs')">
                                    Roll back Body and Legs
                                </button>
                            </div>
                        </div>
                        <h5 class="mb-0 mt-3">Legs First Monsters (reverse)</h5>
                        <div class="row">
                            <div class="col-sm-12 col-md-6 mb-1">
                                <button class="btn btn-primary btn-block" title="Roll back head" @click="rollback('head')">
                                    Roll back Head
                                </button>
                            </div>
                            <div class="col-sm-12 col-md-6 mb-1">
                                <button class="btn btn-primary btn-block" title="Roll back head and body" @click="rollback('head_body')">
                                    Roll back Head and Body
                                </button>
                            </div>
                        </div>
                        <div class="row mt-4">
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
        <add-tag-component
            v-if="activeModal==3" 
            :monster="monster"
            :addingInProgress = addingInProgress
            @close="activeModal=0"
            @add="addTagConfirm">
        </add-tag-component>
        <remove-tag-component
            v-if="activeModal==2" 
            :monster="monster"
            :removingInProgress = removingInProgress
            @close="activeModal=0"
            @remove="removeTagConfirm">
        </remove-tag-component>
        <div v-if="activeModal > 0" class="modal-backdrop fade show"></div>
    </div>
</template>

<script>
    import commentComponent from './Comment';
    import flagMonsterComponent from './FlagMonster';
    import galleryHeaderComponent from './GalleryHeader';
    import removeTagComponent from './RemoveTag';
    import addTagComponent from './AddTag';
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
            everyoneCanUseStore: Number,
            characters: Object
        },
        components : {
            commentComponent,
            flagMonsterComponent,
            galleryHeaderComponent,
            removeTagComponent,
            addTagComponent
        },
        methods: {
            addTag: function(){
                this.activeModal = 3;
                setTimeout(function(){
                    $('#tagName').focus();
                },100)
            },
            removeTag: function(id){
                this.activeModal = 2;
                this.selectedTagId = id;
            },
            addTagConfirm: function(tagName){
                this.addingInProgress = 1;
                this.enteredTagNameToAdd = tagName;
                axios.post('/addTag',{
                    tag_name: this.enteredTagNameToAdd,
                    monster_id: this.monster.id,
                    action: 'addTag'
                })
                .then((response) => {
                    this.currentMonsterTags.push(response.data);
                    this.activeModal = 0;
                    this.addingInProgress = 0;
                    this.enteredTagNameToAdd = '';
                    console.log(response); 
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            removeTagConfirm: function(){
                this.removingInProgress = 1;
                axios.post('/removeTag',{
                    tag_id: this.selectedTagId,
                    monster_id: this.monster.id,
                    action: 'removeTag'
                })
                .then((response) => {
                    for (var i = this.currentMonsterTags.length - 1; i >= 0; i --){
                        if (this.currentMonsterTags[i].id==this.selectedTagId){
                            this.currentMonsterTags.splice(i);
                        }
                    }
                    this.selectedTagId = null;
                    this.activeModal = 0;
                    this.removingInProgress = 0;
                    console.log(response); 
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            toggleEditTagMode: function(){
                this.editTagMode = !this.editTagMode;
            },
            searchByTag: function(id){
                axios.post('/gallery/searchbytag',{
                    tag_id: id,   
                    action: 'searchbytag'    
                })
                .then((response) => {
                    location.href='/monstergrid';
                    console.log(response); 
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            getSegmentImage: function(segment) {
                for (var i = 0; i < this.monster.segments_with_images.length; i ++){
                    if (this.monster.segments_with_images[i].segment == segment){
                        var image = this.monster.segments_with_images[i];
                        return image.image_path ? image.image_path : image.image;
                    }
                }
                return '';
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
            rollback: function(segments) {
                axios.post('/rollback',{
                    monster_id: this.monster.id,  
                    segments: segments,   
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
            prevClicked: function() {
                var url = '/' + this.pageType + '/' + this.prevMonster.id;
                if (this.referrer) url +='?ref=' + this.referrer;
                location.href = url; 
            },
            nextClicked: function() {
                var url = '/' + this.pageType + '/' + this.nextMonster.id;
                if (this.referrer) url +='?ref=' + this.referrer;
                location.href = url; 
            },
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
            referrer: function(){
                var queryString = window.location.search;
                var urlParams = new URLSearchParams(queryString);
                return urlParams.get('ref');
            },
            // createdInLastWeek(){
            //     var d1 = new Date(this.monster.created_at);
            //     var d2 = new Date();
            //     d2.setDate(d2.getDate()-7);
            //     return  d1.getTime() >= d2.getTime();
            // }
        },
        data() {
            return {
                selectedRating: 5,
                activeModal: 0,
                permanentLinkCopied: false,
                imageURLCopied: false,
                currentTakeTwoCount: this.user ? this.user.take_two_count : 0,
                commentComponentKey: 0,
                headerComponentKey: 0,
                editTagMode: false,
                currentMonsterTags: this.monster.tags,
                selectedTagId: null,
                removingInProgress: 0,
                addingInProgress: 0
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
    .monsterTag{
        cursor:pointer;
    }
    h6{
        font-size:1.1rem;
    }
    .border-5{
        border:5px solid #e2f0fb!important;
        box-shadow:none!important;
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
