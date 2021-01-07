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
                        Email me when someone comments on my monster
                    </label>
                </div>
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
                            <button id="saveSettings" type="button" @click="save()" class="btn btn-success form-control btn-block">
                                Save
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
            allowMonsterEmails: Number,
            allowNsfw: Number
        },
        components: {
           
        },
        data() {
            return {
                currentAllowMonsterEmails : this.allowMonsterEmails,
                currentAllowNSFW: this.allowNsfw
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
            save: function() {
                axios.post('/settings/save', { 
                    allow_monster_emails: (this.currentAllowMonsterEmails ? 1 : 0),
                    allow_NSFW: (this.currentAllowNSFW ? 1 : 0)              
                })
                .then((response) => {
                    window.location.href='/home';
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
                var isIOS = (UA && /iphone|ipad|ipod|ios/.test(UA)) || (weexPlatform === 'ios');
                return isIOS;
            }
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
</style>