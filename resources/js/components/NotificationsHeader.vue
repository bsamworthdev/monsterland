<template>
    <div class="container">
        <button id="notificationsButton" type="button" class="btn pb-0 text-left" style="border-radius:20px"  @click="notificationsClicked($event)">
            <i class="fa fa-bell h4"></i> 
            <span v-show="showBadge" class="badge badge-light bg-danger text-white" style="top:-10px;">
                {{ openNotifications.length }}
            </span>
        </button>
        <div v-if="showNotificationsInfo" class="notificationsInfo">
            <notifications-info-box
                :notifications="notifications"
                 @close="showNotificationsInfo=false">
            </notifications-info-box>
        </div>
    </div>
</template>

<script>
    import notificationsInfoBox from './NotificationsInfoBox';
    export default {
        props: {
            user: Object,
            notifications: Array
        },
        components: {
            notificationsInfoBox
        },
        methods: {
            notificationsClicked: function (e){
                this.showNotificationsInfo = true;
                e.stopPropagation();
                axios.post('/updateNotificationsLastViewed',{   
                    'user_id': this.user.id,
                    'action': 'updateLastViewed'
                })
                .then((res) => {
                })
                .catch((error) => {
                    console.log(error);
                });
                
            },
            onClick: function () {
                this.showNotificationsInfo = false;
                this.badgeRequired = false;
            },
        },
        computed: {
            openNotifications: function(){
                var notifications = this.notifications;
                var openNotifications = [];
                for (var i = 0; i < notifications.length; i ++){
                    if (!notifications[i].closed && notifications[i].newSinceLastVisit){
                        openNotifications.push(notifications[i]);
                    }
                }
                return openNotifications;
            },
            showBadge: function(){
                return (this.openNotifications.length > 0 && this.badgeRequired);
            }
        },
        data(){
            return {
                showNotificationsInfo:false,
                badgeRequired:true
            }
        },
        mounted() {
            console.log('Component mounted.');
            document.addEventListener('click', this.onClick);
        },
        beforeDestroy() {
            document.removeEventListener('click', this.onClick);
        },
    }
</script>
<style scoped>
    .notificationsInfo{
        position:absolute;
        top:50px;
        background-color:#FFF;
        border-radius: 0.25rem;
        z-index:999;
        width:550px;
        min-height:100px;
        outline:none!important;
        right:0px;
    }
    #notificationsButton{
        min-width: 70px;
        background-color:#FFF;
        outline:none!important;
    }
    .fa-bell{
        color: rgba(0, 0, 0, 0.5)
    }
    .fa-bell:hover{
        color: rgba(0, 0, 0, 0.9)
    }
    @media (max-width: 576px) {
        .notificationsInfo{
            position:absolute!important;
            left:calc(50% - 130px)!important;
            margin:5px!important;
            width:250px;
        }
    }
</style>
