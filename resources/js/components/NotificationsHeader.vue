<template>
    <div class="container">
        <div id="notificationsButton" title="notifications" type="button" class="btn pb-0 text-left" style="border-radius:20px"  @click="notificationsClicked($event)">
            <i class="fa fa-bell h4"></i> 
            <span v-show="showBadge" class="badge badge-light bg-danger text-white" style="top:-10px;">
                {{ openNotifications.length }}
            </span>
        </div>
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
                if (!this.showNotificationsInfo){
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
                } else {
                    this.showNotificationsInfo = false;
                }    
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
        top:65px;
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
        background-color:transparent!important;
        background:transparent!important;
        white-space:nowrap;
          -webkit-appearance: none;
    }
    .fa-bell{
        color: rgba(0, 0, 0, 0.5)
    }
    .fa-bell:hover{
        color: rgba(0, 0, 0, 0.9)
    }

    @media screen and (max-width: 899px) {
        .notificationsInfo{
            top:50px;
            right:-39px;
        }
    }
    @media screen and (min-width: 375px) and (max-width: 991px) {
        .notificationsInfo{
            margin:5px!important;
            width:315px;
        }
        .notificationsButton{
            min-width:50px;
        }
        .notificationsIcon{
            top:6px;
        }
    }
     @media screen and (max-width: 374px) {
        .notificationsInfo{
            margin:5px!important;
            width:100%;
        }
        .notificationsButton{
            min-width:50px;
        }
        .notificationsIcon{
            padding-left:0px!important;
            padding-right:0px!important;

        }
        #notificationsButton{
            padding-left:0px!important;
            padding-right:0px!important;
        }
        .notificationsInfo{
            right:0px;
        }
    }
</style>
