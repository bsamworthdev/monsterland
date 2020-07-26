<template>
    <div class="m-1">
        <button class="btn btn-info" 
            :disabled="createdByUser||inProgress"
            :class="{'createdByUser':createdByUser,'inProgress':inProgress}" 
            :title="getMonsterTitle()" 
            @click="loadMonster()">
            <i class="fa fa-lock" :class="{'d-none':loggedIn || !isAuthMonster}" ></i> 
            <span :class="{'d-none':!loggedIn || isAuthMonster}" >GUEST: </span> 
            {{ monster.name }}
        </button>                      
    </div>
</template>

<script>
    export default {
        props: {
            monster: Object,
            createdByUser: Boolean,
            inProgress: Boolean,
            loggedIn: Boolean
        },
        methods: {
            loadMonster: function(){
                if (!this.createdByUser && !this.inProgress){
                    if (this.monster.auth) {
                        location.href = '/canvas/' + this.monster.id;
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
                    if (!this.loggedIn && this.isAuthMonster){
                        return 'Log in to add to this monster';
                    } else {
                        return 'Click to draw';
                    }
                } 
            }
        },
        computed: {
            isAuthMonster: function(){
                return this.monster.auth;
            }
        },
        data() {
            return {
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
</style>
