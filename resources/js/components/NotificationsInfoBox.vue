<template>
    <div class="container">
         <div class="card w-100 border-1">
            <div class="card-header">
                Notifications
                <i class="fa fa-times pull-right close" @click="$emit('close')" title="Close"></i>
            </div>
            <div class="card-body">
                <table v-if="filteredNotifications.length > 0" class="table w-100 ">
                    <tr v-for="(notification, index) in filteredNotifications" @click="notificationClicked($event, notification)" 
                        :key="index" :class="[{'justAdded':isRecent(notification.created_at)}, {'unvisited':!notification.closed}]" 
                        :auditId="notification.audit_id">
                        <td>
                            <small>{{ tidyDate(notification.created_at)}}</small>
                        </td>
                        <td v-if="notification.type=='segment_completed' || notification.type=='comment'">
                            <span v-if="notification.user" class="font-weight-bold">{{ notification.user.name }}</span>
                            <span v-else class="font-weight-bold">GUEST</span>
                            {{ notification.action }}
                            <span v-if="notification.type=='segment_completed'" class="font-weight-bold">
                                {{ notification.monster.name }}
                            </span>
                            <span v-else-if="notification.type=='comment'" class="font-weight-bold">
                                {{ notification.monster.name }}
                            </span>
                        </td>
                        <td v-else-if="notification.type=='monster_completed'">
                            Monster completed:
                            <span class="position:absolute font-weight-bold" style="max-width: 7rem" :href="'/gallery/' + notification.monster.id">
                                {{ notification.monster.name }}
                            </span>
                        </td>
                        <td v-else-if="notification.type=='followed_user_monster_completed'">
                            Monster completed (feat {{ notification.user.name }}):
                            <span class="position:absolute font-weight-bold" style="max-width: 7rem" :href="'/gallery/' + notification.monster.id">
                                {{ notification.monster.name }}
                            </span>
                        </td>
                        <td v-else-if="notification.type=='rating'">
                            <span class="font-weight-bold">
                                {{ notification.monster.name }}
                            </span>
                            {{ notification.action }}
                        </td>
                        <td v-else-if="notification.type=='mention'">
                            Someone {{ notification.action }} you 
                            on 
                            <span class="font-weight-bold">
                                {{ notification.monster.name }}
                            </span>
                        </td>
                    </tr>
                </table>
                <div v-else class="m-3">
                    <i>No new notifications</i>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    export default {
        props: {
            notifications: Array,
            user: Object
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
                    interval = Math.floor(interval);
                    return interval + " day" + (interval == 1 ? '' : 's') + " ago";
                }
                interval = seconds / 3600;
                if (interval >= 1) {
                    interval = Math.floor(interval);
                    return interval + " hour" + (interval == 1 ? '' : 's') + " ago";
                }
                interval = seconds / 60;
                if (interval >= 1) {
                    interval = Math.floor(interval);
                    return interval + " min" + (interval == 1 ? '' : 's') + " ago";
                }
                return "Just now";
            },
            notificationClicked: function(e, notification){
                e.stopPropagation();
                axios.post('/closeNotification',{   
                    'auditId': notification.audit_id,
                    'action': 'closeNotification'
                })
                .then((res) => {
                    location.href = "/gallery/" + notification.monster.id;
                })
                .catch((error) => {
                    console.log(error);
                });
            }
        },
        computed: {
            filteredNotifications: function(){
                var arr = []
                var audit_ids = [];
                for (var i = 0; i < this.notifications.length; i++) {
                    var notification = this.notifications[i];
                    var audit_id = notification.audit_id;
                    if (audit_ids.indexOf(audit_id) == -1){
                        audit_ids.push(audit_id);
                        arr.push(notification);
                        if (arr.length >= 12) break;
                    }
                }

                return arr;
            }
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
        margin-top: -4px;
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
    table{
        font-size:0.9rem;
    }
    table tr{
        cursor:pointer;
    }
    table tr:hover{
        opacity:0.7
    }
    table td{
        padding:0.5rem;
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
