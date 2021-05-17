<template>
    <div class="container pt-1">
        <div v-for="(account, index) in socialMediaAccounts" :key="index" class="account">
            <a :href="getUrl(account)" :title="getUrl(account)" target="_blank">
                <img class="icon" :src="'/images/' + account.account_type + '.png'">
                <span v-show="editMode">
                    {{ account.account_name }}
                </span>
            </a>
        </div>
        <div v-if="socialMediaAccounts.length == 0">
            <i>none</i>
        </div>
        <a id="editLink" @click="openEditModal()">
            <i class="pl-3 fa fa-pen"></i>
        </a>
        <edit-social-media-accounts-component
             v-if="activeModal==1" 
             @close="activeModal=0"
            :socialMediaAccounts = "socialMediaAccounts">
        </edit-social-media-accounts-component>
        <div v-if="activeModal > 0" class="modal-backdrop fade show"></div>
    </div>
</template>

<script>
    import editSocialMediaAccountsComponent from './EditSocialMediaAccounts';
    export default {
        props: {
            socialMediaAccounts: Array
        },
        components: {
            editSocialMediaAccountsComponent
        },
        methods: {
            getUrl: function(account){
                switch (account.account_type) {
                    case 'facebook':
                        return 'https://facebook.com/' + account.account_name;
                        break;
                    case 'instagram':
                        return 'https://instagram.com/' + account.account_name;
                        break;
                    case 'twitter':
                        return 'https://twitter.com/' + account.account_name;
                        break;
                    case 'twitch':
                        return 'https://twitch.com/' + account.account_name;
                        break;
                    case 'discord':
                        return 'https://discord.com/' + account.account_name;
                        break;
                    case 'youtube':
                        return 'https://youtube.com/' + account.account_name;
                        break;
                    case 'reddit':
                        return 'https://reddit.com/u/' + account.account_name;
                        break;
                }
            },
            openEditModal: function(){
                this.activeModal = 1;
            }
        },
        computed: {
            
        },
        data(){
            return {
                editMode: false,
                activeModal: 0
            }
        },
        mounted() {
            console.log('Component mounted.');
           
        },
    }
</script>
<style scoped>
    .container{
        display:flex;
        flex-direction: row;
        justify-content: flex-end;
    }
   .icon{
       height:20px;
       width:20px;
   }
   .account{
       padding-left:15px;
       white-space: nowrap;
   }
   .account:hover{
       cursor:pointer;
   }
   .account span{
       padding-left:5px;
       vertical-align: middle;
   }
   #editLink{
       cursor:pointer;
   }
</style>
