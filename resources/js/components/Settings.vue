<template>
    <div class="mt-1 ml-5 mr-5">
        <form method="POST" class="form-horizontal">
            <div class="form-group">
                <div class="custom-control custom-switch mb-2" :title="nsfwTooltip">
                    <!-- <label for="includeNSFW" title="Show 'Not Safe For Work' monsters">Show NSFW
                        <input type="checkbox" id="includeNSFW" :checked="allowNSFW" onclick="includeNSFW_clicked(event)" class="form-check-input">
                    </label> -->
                    <input type="checkbox" :disabled="isIOS()" id="includeNSFW" name="includeNSFW" @change="toggleAllowNSFW()" :checked="allowNsfw" class="custom-control-input">
                    <label class="custom-control-label" for="includeNSFW">
                        Show NSFW (Not Safe For Work) content 
                        <div class="text text-danger"> (Age 18+ only)</div>
                    </label>
                </div>
            </div>
            <div class="form-group">
                <div class="custom-control custom-switch mb-2">
                    <input type="checkbox" name="completeEmail" @change="toggleEmailOnComplete()" :checked="allowMonsterEmails" class="custom-control-input" id="completeEmail">
                    <label class="custom-control-label" for="completeEmail">
                        Email Notifications
                        <div class="text text-secondary">Email me when someone comments on my monster</div>
                    </label>
                </div>
            </div>
            <div class="form-group">
                <div class="custom-control custom-switch mb-2">
                    <input type="checkbox" name="followerNotify" @change="toggleFollowerNotify()" :checked="followerNotify" class="custom-control-input" id="followerNotify">
                    <label class="custom-control-label" for="followerNotify">
                        Follower Notifications
                        <div class="text text-secondary">Show Notifications for people I follow</div>
                    </label>
                </div>
            </div>
            <div class="form-group" v-if="isPatron">
                <div class="custom-control custom-switch mb-2">
                    <input type="checkbox" name="peekView" @change="togglePeekViewActivated()" :checked="peekViewActivated" class="custom-control-input" id="peekView">
                    <label class="custom-control-label" for="peekView">
                        Peek View
                        <div class="text text-secondary"> Show <i class="fas fa-eye"></i> when the section artist peeked at a previous section.</div>
                    </label>
                </div>
            </div>

            <div class="form-group" v-if="userId==1">
                <!-- <div class="row mt-4">
                    <div class="col-sm-12 col-md-6 mb-1">
                        <button class="btn btn-info btn-block" title="Save to redis" @click="saveToRedis()">
                            <i class="fa fa-save"></i> Save date to redis
                        </button>
                    </div>
                    <div class="col-sm-12 col-md-6 mb-1">
                        <button class="btn btn-info btn-block" title="Fetch from redis" @click="fetchFromRedis()">
                            <i class="fa fa-download"></i> Fetch date from redis
                        </button>
                    </div>
                </div> -->
                    <div class="custom-control custom-switch mb-2">
                        <input type="checkbox" name="redisActive" @change="toggleRedisActivated()" :checked="redisActivated" class="custom-control-input" id="redisActive">
                        <label class="custom-control-label" for="redisActive">
                            Redis
                        </label>
                    </div>
                    <button id="flushRedis" class="btn btn-info" :disabled="flushingInProgress" v-show="currentRedisActivated" title="Flush Redis keys" @click="flushRedis()">
                        <div class="spinner-border" v-if="flushingInProgress" role="status">
                            <span class="sr-only"> Flushing...</span>
                        </div>
                        <span v-else>
                            <i class="fas fa-toilet"></i> Flush Redis keys
                        </span>
    
                    </button>
            </div>

            
            <div class="form-group pt-5"> 
                <div class="container">
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <button id="saveSettings" type="button" @click="backClick()" class="btn btn-primary form-control btn-block">
                                Return to Lobby
                            </button>
                        </div>
                        <div class="col-md-6 col-12">
                            <button id="saveSettings" :disabled="savingInProgress" type="button" @click="save()" class="btn btn-success form-control btn-block">
                                <div class="spinner-border" v-if="savingInProgress" role="status">
                                    <span class="sr-only"> Saving...</span>
                                </div>
                                <span v-else>
                                    Save
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>    
        </form>
    </div>
</template>

<script>

    export default {
        props: {
            userId: Number,
            isPatron: Number,
            allowMonsterEmails: Number,
            allowNsfw: Number,
            peekViewActivated: Number,
            followerNotify: Number,
            redisActivated: Number
        },
        components: {
           
        },
        data() {
            return {
                currentAllowMonsterEmails : this.allowMonsterEmails,
                currentAllowNSFW: this.allowNsfw,
                currentPeekViewActivated: this.peekViewActivated,
                currentFollowerNotify: this.followerNotify,
                currentRedisActivated: this.redisActivated,
                flushingInProgress: false,
                savingInProgress: false
            }
        },
        mounted() {
            console.log('Component mounted.');
            $(function () {
                $('[data-toggle="tooltip"]').tooltip()
            }) 
        },
        methods: { 
            toggleEmailOnComplete: function() {
                this.currentAllowMonsterEmails = this.currentAllowMonsterEmails ? 0 : 1;
            },
            toggleAllowNSFW: function(){
                this.currentAllowNSFW = this.currentAllowNSFW ? 0 : 1;
            },
            togglePeekViewActivated: function(){
                this.currentPeekViewActivated = this.currentPeekViewActivated ? 0 : 1;
            },
            toggleFollowerNotify: function(){
                this.currentFollowerNotify = this.currentFollowerNotify ? 0 : 1;
            },
            toggleRedisActivated: function(){
                this.currentRedisActivated = this.currentRedisActivated ? 0 : 1;
            },
            save: function() {
                var _this = this;
                _this.savingInProgress = true;
                axios.post('/settings/save', { 
                    allow_monster_emails: (this.currentAllowMonsterEmails ? 1 : 0),
                    allow_NSFW: (this.currentAllowNSFW ? 1 : 0),
                    peek_view_activated: (this.currentPeekViewActivated ? 1 : 0),
                    follower_notify: (this.currentFollowerNotify ? 1 : 0),
                    redis_activated: (this.currentRedisActivated ? 1 : 0)                        
                })
                .then((response) => {
                    _this.savingInProgress = false
                    console.log(response); 
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            backClick: function() {
                location.href='/home';
            },
            isIOS: function() {
                var inBrowser = typeof window !== 'undefined';
                var inWeex = typeof WXEnvironment !== 'undefined' && !!WXEnvironment.platform;
                var weexPlatform = inWeex && WXEnvironment.platform.toLowerCase();
                var UA = inBrowser && window.navigator.userAgent.toLowerCase();
                var safari = (UA && /safari/.test(UA)) || (weexPlatform === 'ios');
                var ios = (UA && /iphone|ipad|ipod|ios/.test(UA)) || (weexPlatform === 'ios');

                if(ios) {
                    if ( safari ) {
                        return false;
                    } else if ( !safari ) {
                        return true;
                    };
                } else {
                    return false;
                };
            },
            // saveToRedis: function(level){
            //     axios.post('/saveToRedis',{
            //         monster_id: this.monster.id,
            //         action: 'saveToRedis',
            //         level: level          
            //     })
            //     .then((response) => {
            //         console.log(response); 
            //     })
            //     .catch((error) => {
            //         console.log(error);
            //     });
            // },
            // fetchFromRedis: function(level){
            //     axios.post('/fetchFromRedis',{
            //         monster_id: this.monster.id,
            //         action: 'fetchFromRedis'       
            //     })
            //     .then((response) => {
            //         console.log(response.data); 
            //     })
            //     .catch((error) => {
            //         console.log(error);
            //     });
            // },
            flushRedis: function(level){
                var _this = this;
                _this.flushingInProgress = true;
                axios.post('/flushRedis',{
                    action: 'flushRedis'      
                })
                .then((response) => {
                    _this.flushingInProgress = false;
                    console.log(response.data); 
                })
                .catch((error) => {
                    _this.flushingInProgress = false;
                    alert('error- not flushed');
                    console.log(error);
                });
            },
        },
        computed: {
            nsfwTooltip: function(){
                if (this.isIOS()){
                    return 'Enable NSFW switch via https://monsterland.net/settings';
                } else{
                    return '';
                }
            }
        }
    }
</script>
<style scoped>

    .btn-info:not(.active){
        background-color:#DDEDFA!important;
    }
    .btn-info:not(.active):hover{
        color:#C0C0C0;
    }
    .spinner-border{
        width:1.5rem;
        height:1.5rem;
    }
    #flushRedis{
        width:200px;
    }
</style>