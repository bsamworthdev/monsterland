<template>
    <div class="container">
         <div class="card w-100 border-0">
            <div class="card-header">
                Notifications
                <i class="fa fa-times pull-right close" @click="$emit('close')" title="Close"></i>
            </div>
            <div class="card-body">
                <table class="table w-100 ">
                    <tr v-for="(change, index) in notifications" :key="index" :class="[{'justAdded':isRecent(change.created_at)}, {'unvisited':!change.closed}]">
                        <td>
                        <small>{{ tidyDate(change.created_at)}}</small>
                        </td>
                        <td v-if="change.type=='segment_completed' || change.type=='comment'">
                        <a v-if="change.user" class="position:absolute" style="max-width: 7rem" :href="'/monsters/' + change.user.id">
                            {{ change.user.name }}
                        </a>
                        <span v-else>GUEST</span>
                        {{ change.action }}
                        <a v-if="change.type=='segment_completed'" class="position:absolute" style="max-width: 7rem" :href="'/canvas/' + change.monster.id">
                            {{ change.monster.name }}
                        </a>
                        <a v-else-if="change.type=='comment'" class="position:absolute" style="max-width: 7rem" :href="'/gallery/' + change.monster.id">
                            {{ change.monster.name }}
                        </a>
                        </td>
                        <td v-else-if="change.type=='monster_completed'">
                        {{ change.action }}
                        <a class="position:absolute" style="max-width: 7rem" :href="'/gallery/' + change.monster.id">
                            {{ change.monster.name }}
                        </a>
                        </td>
                        <td v-else-if="change.type=='rating'">
                        <a class="position:absolute" style="max-width: 7rem" :href="'/gallery/' + change.monster.id">
                            {{ change.monster.name }}
                        </a>
                        {{ change.action }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</template>

<script>

    export default {
        props: {
            notifications: Array
        },
        components: {
            
        },
        methods: {
            isRecent: function(date){
                var unix_timestamp = Date.parse(date);
                var seconds = Math.floor((new Date() - unix_timestamp) / 1000);
                return (seconds < 10);
            },
            tidyDate:function(date){
                var unix_timestamp = Date.parse(date);
                var seconds = Math.floor((new Date() - unix_timestamp) / 1000);
                var interval = seconds / 31536000;

                if (interval >= 1) {
                    interval =Math.floor(interval);
                    return interval + " year" + (interval == 1 ? '' : 's') + " ago";
                }
                interval = seconds / 2592000;
                if (interval >= 1) {
                    interval =Math.floor(interval);
                    return interval + " month" + (interval == 1 ? '' : 's') + " ago";
                }
                interval = seconds / 86400;
                if (interval >= 1) {
                    interval =Math.floor(interval);
                    return interval + " day" + (interval == 1 ? '' : 's') + " ago";
                }
                interval = seconds / 3600;
                if (interval >= 1) {
                    interval =Math.floor(interval);
                    return interval + " hour" + (interval == 1 ? '' : 's') + " ago";
                }
                interval = seconds / 60;
                if (interval >= 1) {
                    interval =Math.floor(interval);
                    return interval + " min" + (interval == 1 ? '' : 's') + " ago";
                }
                return "Just now";
            },
        },
        computed: {
            
        },
        data(){
            return {
            }
        },
        mounted() {
            console.log('Component mounted.');
        },
    }
</script>
<style scoped>
    .container{
        width: 100%!important;
        padding: 0px;
    }
    .card-header{
        padding: 0.15rem 1.25rem;
        font-size: 14px;
    }
    .card-body{
        padding: 0.25rem;
        margin-top: -5px;
        white-space:normal;
    }
    .close{
        font-size:12px;
        padding:5px;
        cursor:pointer;
    }

    .justAdded{
        background-color:lightgreen;
        transition: background-color 1s linear;
        -moz-transition: background-color 1s linear;  
        -webkit-transition: background-color 1s linear; 
        -ms-transition: background-color 1s linear; 
    }

    .justAdded {
        background-color: lightgreen;
        animation: fadeout 1s forwards;
        animation-delay: 2s;
        -moz-animation: fadeout 1s forwards;
        -moz-animation-delay: 2s;
        -webkit-animation: fadeout 1s forwards;
        -webkit-animation-delay: 2s;
        -o-animation: fadeout 1s forwards;
        -o-animation-delay: 2s;
    }

    .unvisited{
        background-color:lightskyblue;
    }

    @keyframes fadeout {
        to {
            background-color:transparent;
        }

    }
    @-moz-keyframes fadeout {
        to {
            background-color: transparent;
        }
    }
    @-webkit-keyframes fadeout {
        to {
            background-color: transparent;
        }
    }
    @-o-keyframes fadeout {
        to {
            background-color: transparent;
        }
    }
</style>
