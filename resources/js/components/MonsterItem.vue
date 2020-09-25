<template>
    <div class="m-1">
        <button class="btn btn-info monsterButton" 
            :disabled="createdByUser||inProgress"
            :class="{'createdByUser':createdByUser,'inProgress':inProgress, 'proMonster': isProMonster, 'nsfwMonster': isNSFWMonster}" 
            :title="getMonsterTitle()" 
            @click="loadMonster()">
            <i class="fa fa-lock" :class="{'d-none':hidePadlock}" ></i> 
            <span class="guestLabel" :class="{'d-none':hideGuestLabel}" >GUEST: </span>
            <span class="proLabel" :class="{'d-none':hideProLabel}" >
                <i class="fa fa-star"></i> 
            </span>
            {{ monster.name }}
            <span class="nsfwLabel" :class="{'d-none':hideNSFWLabel}" >NSFW!</span>
            <a v-if="this.allowPeek.includes(userId)" :href="'/gallery/' + monster.id" @click="$event.stopPropagation();">
                <i class="fa fa-eye"></i>
            </a>   
        </button>                   
    </div>
</template>

<script>
    export default {
        props: {
            monster: Object,
            createdByUser: Boolean,
            inProgress: Boolean,
            loggedIn: Boolean,
            userIsVip: Number,
            userId: Number
        },
        methods: {
            loadMonster: function(){
                if (!this.createdByUser && !this.inProgress){
                    if (this.monster.auth) {
                        if (this.monster.vip){
                            if (this.userIsVip){
                                location.href = '/canvas/' + this.monster.id;
                            }
                        } else {
                            location.href = '/canvas/' + this.monster.id;
                        }
                    } else {
                        location.href = '/nonauth/canvas/' + this.monster.id;
                    }
                        
                }
            },
            getMonsterTitle: function(){
                if (this.createdByUser){
                    return 'You cannot add to your own monster';
                } else if(this.inProgress){
                    return 'In Progress...';
                } else {
                    // if (!this.loggedIn){
                    //     if (this.isAuthMonster) {
                    //         if (this.isAuthMonster) {
                    //         return 'Log in to add to this monster';
                    //     } else {
                    //         return 'Click to draw';
                    //     }
                    // } else {
                    //     return 'Click to draw';
                    // }

                    if (this.isAuthMonster) {
                        if (this.isProMonster) {
                            if (this.userIsVip == 1){
                                return 'Click to draw';
                            } else {
                                return 'You don\'t have access to Pro monsters';
                            }
                        } else {
                            if (this.loggedIn){
                                return 'Click to draw';
                            } else {
                                return 'Log in to add to this monster';
                            }
                        }
                    }
                    else {
                        return 'Click to draw';
                    }
                } 
            }
        },
        computed: {
            isAuthMonster: function(){
                return this.monster.auth;
            },
            isProMonster: function(){
                return this.monster.vip;
            },
            isNSFWMonster: function(){
                return this.monster.nsfw;
            },
            hidePadlock: function(){
                var resp = false;
                if (this.loggedIn){
                    if (this.isProMonster){
                        if (this.userIsVip == 1) {
                            resp = true;
                        }
                    } else {
                        resp = true;
                    }
                } else {
                    if (!this.isAuthMonster){
                        resp = true;
                    }
                }
                return resp;
            }, 
            hideGuestLabel: function(){
                if (this.loggedIn){
                    if (this.isAuthMonster){
                        return true;
                    }
                } else {
                    return true;
                }
            },
            hideProLabel: function(){
                var resp = false;
                if (!this.isProMonster){
                    resp = true;
                }
                return resp;
            },
            hideNSFWLabel: function(){
                var resp = false;
                if (!this.isNSFWMonster){
                    resp = true;
                }
                return resp;
            },
        },
        data() {
            return {
                allowPeek: [1,2]
            }
        },
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>

<style scoped>
    .inProgress{
        background-color:rgba(192, 192, 192, 0.589);
    }
    .createdByUser{
        background-color:#FFF;
        opacity:1!important;
        border:none;
    }
    .monsterButton{
        display:inline;
        white-space:nowrap;
        min-height:40px;
    }
    .proMonster{
        background-color:gold;
    }
    .proMonster.inProgress{
        background-color:rgb(214, 210, 183);
    }
    .proMonster.createdByUser{
        color:rgb(155, 132, 0);
        background-color:#FFFFFF
    }
    .nsfwMonster{
        border:1px solid red;
    }
    .nsfwLabel{
        color:red;
        font-weight:bold;
        font-style:italic;
    }
</style>
