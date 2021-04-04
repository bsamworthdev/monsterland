<template>
    <div class="container" id="waitingRoomContainer" :class="{ 'modal-open': activeModal > 0 }">
        <div v-if="user_id==1 && monitoredMonsters.length>0" class="row justify-content-center">
            <div class="alert alert-danger w-100">
                <h5>Requires Validating</h5>
                <p>The latest segment of these monsters needs to be validated:</p>
                <div class="float-left" v-for="monster in monitoredMonsters" :key="monster.id">
                    <monster-item-component
                        :monster="monster"
                        :created-by-user="createdByUser(monster, 'legs')"
                        :in-progress="false"
                        :logged-in="true"
                        :user-is-vip="user_is_vip"
                        :user-id="user_id"
                        :flagged-as-spam="true">
                    </monster-item-component>
                </div>
            </div>
        </div>
        <div v-if="user_id==1 && takeTwoMonsters.length>0" class="row justify-content-center">
            <div class="alert alert-info w-100">
                <h5>Take Two Requests</h5>
                <p>The following monsters have had "Take Two" requests created:</p>
                <div class="float-left" v-for="monster in takeTwoMonsters" :key="monster.id">
                    <monster-item-component
                        :monster="monster"
                        :created-by-user="createdByUser(monster, 'legs')"
                        :in-progress="false"
                        :logged-in="true"
                        :user-is-vip="user_is_vip"
                        :user-id="user_id"
                        :flagged-as-spam="true">
                    </monster-item-component>
                </div>
            </div>
        </div>
        <div v-if="user_id==1 && flaggedMonsters.length>0" class="row justify-content-center">
            <div class="alert alert-danger w-100">
                <h5>Rollbacks</h5>
                <p>The following monsters may require rollback:</p>
                <div class="float-left" v-for="monster in flaggedMonsters" :key="monster.id">
                    <monster-item-component
                        :monster="monster"
                        :created-by-user="createdByUser(monster, 'legs')"
                        :in-progress="false"
                        :logged-in="true"
                        :user-is-vip="user_is_vip"
                        :user-id="user_id"
                        :flagged-as-spam="true">
                    </monster-item-component>
                </div>
            </div>
        </div>
        <div v-if="user_id==1 && flaggedCommentMonsters.length>0" class="row justify-content-center">
            <div class="alert alert-danger w-100">
                <h5>Spam comments</h5>
                <p>The following monsters have comments marked as spam:</p>
                <div class="float-left" v-for="monster in flaggedCommentMonsters" :key="monster.id">
                    <monster-item-component
                        :monster="monster"
                        :created-by-user="createdByUser(monster, 'legs')"
                        :in-progress="false"
                        :logged-in="true"
                        :user-is-vip="user_is_vip"
                        :user-id="user_id"
                        :flagged-as-spam="true">
                    </monster-item-component>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div id="main-container" class="col-md-12">

                <div class="container">
                    <div class="row mb-2">
                        <h4>Start A New Monster...</h4>
                    </div>
                     <div class="row mb-4">
                        <div class="col-md-3">
                            <button class="btn btn-lg btn-success text-nowrap" @click="openCreateMonsterModal($event)">
                                <i class="fa fa-pastafarianism mr-1"></i> Create Monster
                            </button>
                        </div>                      
                    </div>
                    <div class="row mb-2">
                        <h4>...or finish someone else's</h4>
                    </div>
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-12">
                                    <h5>
                                        Monsters Needing Bodies ({{ monstersAwaitingBodies.length }})
                                        <button v-if="autoRefreshExpired" class="btn btn-info btn-sm float-right" @click="refresh">
                                            <i class="fas fa-sync-alt"></i> Refresh
                                        </button>
                                    </h5>
                                </div>                      
                            </div>
                        </div>
                        <div class="card-body mb-0 pb-1">
                            <div class="row">
                                <div v-if="monstersAwaitingBodies.length > 0">
                                    <div style="float:left;" v-for="(monster ,index) in monstersAwaitingBodies" :key="index">
                                        <monster-item-component 
                                            v-show="index < segmentLimit || showMoreBodies"
                                            :monster="monster"
                                            :created-by-user="createdByUser(monster,'body')"
                                            :in-progress="inProgress(monster)"
                                            :logged-in="true"
                                            :user-is-vip="user_is_vip"
                                            :user-id="user_id">
                                        </monster-item-component>
                                    </div>
                                </div>
                                <div v-if="monstersAwaitingBodies.length > 0 && monstersAwaitingBodies.length > segmentLimit " class="w-100 mt-1" >
                                    <button class="btn btn-light btn-block" v-if="!showMoreBodies" @click="toggleShowMoreBodies">
                                        <i class="fa fa-sort-down"></i>
                                        View more...
                                    </button>
                                    <button class="btn btn-light btn-block" v-if="showMoreBodies" @click="toggleShowMoreBodies">
                                        <i class="fa fa-sort-up"></i>
                                        View less...
                                    </button>
                                </div>
                                <div v-if="monstersAwaitingBodies.length == 0">
                                    <i class="noRecords">No monsters here!</i>
                                </div>
                            </div>
                        </div>
                    </div>
                     <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-12">
                                    <h5>
                                        Monsters Needing Legs ({{ monstersAwaitingLegs.length }})
                                        <button v-if="autoRefreshExpired" class="btn btn-info btn-sm float-right" @click="refresh">
                                            <i class="fas fa-sync-alt"></i> Refresh
                                        </button>
                                    </h5>
                                </div>                      
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div v-if="monstersAwaitingLegs.length > 0">
                                    <div class="float-left" v-for="(monster,index) in monstersAwaitingLegs" :key="index">
                                        <monster-item-component
                                            v-show="index < segmentLimit || showMoreLegs"
                                            :monster="monster"
                                            :created-by-user="createdByUser(monster, 'legs')"
                                            :in-progress="inProgress(monster)"
                                            :logged-in="true"
                                            :user-is-vip="user_is_vip"
                                            :user-id="user_id">
                                        </monster-item-component>
                                    </div>
                                </div>
                                <div v-if="monstersAwaitingLegs.length > 0 && monstersAwaitingLegs.length > segmentLimit " class="w-100" mt-1 >
                                    <button class="btn btn-light btn-block" v-if="!showMoreLegs" @click="toggleShowMoreLegs">
                                        <i class="fa fa-sort-down"></i>
                                        View more...
                                    </button>
                                    <button class="btn btn-light btn-block" v-if="showMoreLegs" @click="toggleShowMoreLegs">
                                        <i class="fa fa-sort-up"></i>
                                        View less...
                                    </button>
                                </div>
                                <div v-if="monstersAwaitingLegs.length == 0">
                                    <i class="noRecords">No monsters here!</i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
        <create-monster-component
            v-if="activeModal==1" 
            :user_allows_nsfw="user_allows_nsfw"
            :user_is_vip="user_is_vip"
            :random-words= "randomWords"
            @close="activeModal=0" >
        </create-monster-component>
        <award-weekly-trophies-component
            v-if="activeModal==2" 
            @close="activeModal=0" >
        </award-weekly-trophies-component>
        <div v-if="user_id==1" class="card mt-5">
            <div class="card-body bg-warning">
                <div class="row mt-12">
                    <div class="col-sm-12 mb-1">
                        <div class="container">
                            <div class="row">
                                <button class="btn btn-primary btn-block" title="Unblock locked monsters" @click="unblockLockedMonsters">
                                    Unblock locked monsters
                                </button>
                            </div>
                            <div class="row">
                                <div class="btn-group w-100">
                                    <button class="btn btn-primary w-100" title="Create missing PNGs" @click="createMonsterPngs">
                                        Create missing pngs
                                    </button>  
                                    <button class="btn btn-primary w-100" title="Create missing PNGs" @click="createMissingThumbnails">
                                        Create missing thumbnails
                                    </button>  
                                </div>
                            </div>
                            <div class="row">
                                <div class="btn-group w-100">
                                    <button class="btn btn-primary w-100" title="Award Trophies" @click="awardTrophies">
                                        Award Missing Trophies
                                    </button>  
                                    <button class="btn btn-primary w-100" title="Award Trophies" @click="openWeeklyTrophiesModal">
                                        Award Weekly Trophies
                                    </button>  
                                </div>
                            </div>
                            <div class="row">
                                <div class="btn-group w-100">
                                    <button class="btn btn-primary w-100" title="Award Trophies" @click="removeOldB64Images">
                                        Remove old base64 images
                                    </button>  
                                    <button class="btn btn-primary w-100" title="Award Trophies" @click="convertB64Images">
                                        Turn base64 codes into images
                                    </button>  
                                </div>
                            </div>
                            <div class="row">
                                <div class="btn-group w-100">
                                    <button class="btn btn-primary w-100" title="Send Webhook" @click="sendWebhook">
                                        Send Webhook
                                    </button>   
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="activeModal > 0" class="modal-backdrop fade show"></div>
    </div>

</template>

<script>
    import monsterItemComponent from './MonsterItem' ;
    import createMonsterComponent from './CreateMonster' ;
    import awardWeeklyTrophiesComponent from './AwardWeeklyTrophies' ;
    export default {
        props: {
            flaggedMonsters: Array,
            flaggedCommentMonsters: Array,
            monitoredMonsters: Array,
            takeTwoMonsters: Array,
            monsters: Array,
            user_id: Number,
            user_is_vip: Number,
            user_allows_nsfw: Number,
            randomWords: Object,
            dailyActionCount: Number
        },
        components: {
            monsterItemComponent,
            createMonsterComponent,
            awardWeeklyTrophiesComponent
        },
        methods: {
            openCreateMonsterModal: function(){
                this.activeModal = 1;
            },
            createMonster: function(e){
                var monsterName = document.getElementById('monsterName').value;
                e.preventDefault();
                axios.post('/createNewMonster',{   
                    'name' : monsterName         
                })
                .then((response) => {
                    // var url = '/canvas/' + response.data.id;
                    // window.location.href = url;
                    // console.log(response); 
                })
                .catch((error) => {
                    console.log(error);
                });
               
            },
            createdByUser: function (monster, currentSegment){
                for (var i = 0; i < monster.segments.length; i++){
                    if (monster.segments[i].created_by == this.user_id){
                        switch (currentSegment) {
                            case 'body':
                                if (monster.segments[i].segment == 'head'){
                                    return true;
                                }
                            break;
                            case 'legs':
                                if (monster.segments[i].segment == 'body'){
                                    return true;
                                }
                            break;
                        }

                        
                    }
                }
                return false;
            },
            inProgress: function (monster){
                if (monster.in_progress == 1 && !monster.abandoned){
                    return true;
                } else {
                    return false
                }
            },
            unblockLockedMonsters: function() {
                axios.post('/unblockLockedMonsters',{
                    action: 'unblock'           
                })
                .then((response) => {
                    location.reload();
                    console.log(response); 
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            createMonsterPngs: function() {
                axios.post('/createMonsterPngs',{
                    action: 'createpngs'           
                })
                .then((response) => {
                    location.reload();
                    console.log(response); 
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            createMissingThumbnails: function(){
                axios.post('/createMissingThumbnails',{
                    action: 'createMissingThumbnails'           
                })
                .then((response) => {
                    location.reload();
                    console.log(response); 
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            refresh: function() {
                var _this = this;

                var path = '/getDailyActionCount';
                axios.get(path).then(function(response) {
                    var count = response.data;

                    if (count != _this.currentDailyActionCount){
                        var path = '/fetchMonsters';
                        axios.get(path).then(function(response) {
                            _this.loadedMonsters = response.data;
                        });
                        _this.currentDailyActionCount = count;
                    }
                });   
                
            },
            awardTrophies: function() {
                axios.post('/awardTrophies',{
                    action: 'awardtrophies'           
                })
                .then((response) => {
                    location.reload();
                    console.log(response); 
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            openWeeklyTrophiesModal: function(){
                this.activeModal = 2
            },
            toggleShowMoreBodies: function(){
                this.showMoreBodies = !this.showMoreBodies;
            },
            toggleShowMoreLegs: function(){
                this.showMoreLegs = !this.showMoreLegs;
            },
            removeOldB64Images: function(){
                axios.post('/removeOldB64Images',{
                    action: 'removeOldB64Images'           
                })
                .then((response) => {
                    location.reload();
                    console.log(response); 
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            convertB64Images: function(){
                axios.post('/convertB64Images',{
                    action: 'convertB64Images'           
                })
                .then((response) => {
                    location.reload();
                    console.log(response); 
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            sendWebhook: function(){
                axios.post('https://discord.com/api/webhooks/828349688247484476/yh_yD6f9efWiYQ8fbBHc3vfPTtow5zPQrohSdJ6xwmOdLvHUyPZlNGF3GwBcZi6Jmp_1',{
                    username: 'monsterland_automated',
                    content: 'this is a test'           
                })
                .then((response) => {
                    console.log(response); 
                })
                .catch((error) => {
                    console.log(error);
                });
            }
        },
        computed: {
            monstersAwaitingBodies: function (){
                return this.loadedMonsters.filter(i => (i.status === 'awaiting body'))
            },
            monstersAwaitingLegs: function (){
                return this.loadedMonsters.filter(i => (i.status === 'awaiting legs'))
            },
            autoRefreshExpired: function(){
                return this.refreshCount >= this.refreshCountLimit;
            }
        },
        data() {
            return {
                activeModal: 0,
                loadedMonsters: this.monsters,
                segmentLimit:15,
                showMoreBodies: false,
                showMoreLegs: false,
                refreshCount: 0,
                refreshCountLimit: 300,
                timer: null,
                currentDailyActionCount: this.dailyActionCount
            }
        },
        mounted() {
            console.log('Component mounted.')
            
            const self = this;  
            this.timer =setInterval(function(){
                if (self.refreshCount < self.refreshCountLimit){
                    self.refresh();
                    self.refreshCount ++;
                } else {
                    clearInterval(self.timer);
                }

            }, 10000);
            
        },
    }
</script>

<style scoped>
.noRecords{
    padding:10px;
}
</style>
