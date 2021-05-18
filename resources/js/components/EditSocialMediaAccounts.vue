<template>
    <modal @close="close">
        <template v-slot:header>
            <h5 class="modal-title">My Accounts</h5>
            <button type="button" class="close" @click="$emit('close')" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </template>

        <template v-slot:body>
            <form @submit="onSubmit" action="/socialMediaAccounts/update" method="POST" class="form-horizontal">
                <div class="container">
                    <div v-for="(account, index) in currentAccounts" :key="index" class="row account pb-3">
                        <div class="col-4 text-right labelContainer">
                            <span class="label label-default ml-2">{{ account.url_prefix }}</span>
                        </div>
                        <div class="col-4 pl-0">
                            <input type="text" :name="'accounts[' + account.account_type + ']'" v-model="account.account_name" class="form-control">
                        </div>
                        <div class="col-4">
                            <img class="icon" :src="'/images/' + account.account_type + '.png'">
                        </div>
                    </div>
                </div>
                <div class="form-group"> 
                    <button id="saveAccounts" @click="save"  type="submit" class="btn btn-success form-control">
                        <div class="spinner-border" v-if="saveInProgress" role="status">
                            <span class="sr-only">Saving...</span>
                        </div>
                        <span v-else>
                            Save
                        </span>
                    </button>
                </div> 
            </form>
        </template>

        <template v-slot:footer>
            <button type="button" class="btn btn-default" @click="$emit('close')">Close</button>
        </template>
    </modal>
</template>

<script>
    import modal from './Modal' ;

    export default {
        props: {
            accounts: Array
        },
        components: {
            modal
        },
        data() {
            return {
                wordText:'',
                availableAccountTypes: ['facebook','instagram','twitter','twitch','discord','youtube','reddit'],
                saveInProgress: false,
                currentAccounts: []
            }
        },
        mounted() {
            console.log('Component mounted.');
            this.setUpCurrentAccounts();
        },
        methods: { 
            setUpCurrentAccounts: function(){
                var accountName;
                var accountType;
                var urlPrefix;
                for(var i = 0; i < this.availableAccountTypes.length; i++){
                    var account = {}; 
                    accountType = this.availableAccountTypes[i];
                    accountName = this.getAccountName(accountType);
                    urlPrefix = this.getUrlPrefix(accountType);
                    account['account_type'] = accountType;
                    account['account_name'] = accountName;
                    account['url_prefix'] = urlPrefix;
                    this.currentAccounts.push(account);
                }
            },
            getUrlPrefix: function(accountType){
                switch (accountType) {
                    case 'facebook':
                        return 'https://facebook.com/groups/';
                        break;
                    case 'instagram':
                        return 'https://instagram.com/';
                        break;
                    case 'twitter':
                        return 'https://twitter.com/';
                        break;
                    case 'twitch':
                        return 'https://twitch.com/';
                        break;
                    case 'discord':
                        return 'https://discord.com/';
                        break;
                    case 'youtube':
                        return 'https://youtube.com/';
                        break;
                    case 'reddit':
                        return 'https://reddit.com/u/';
                        break;
                }
            },
            getAccountName: function(accountType){
                for(var i = 0; i < this.accounts.length; i++){
                    if (this.accounts[i].account_type == accountType){
                        return this.accounts[i].account_name;
                    }
                }
                return '';
            },
            save: function() {
                this.saveInProgress = true;
            }
        }
    }
</script>
<style scoped>
    .modal{
        color:#000000!important;
    }
    .icon{
        height:35px;
        width:35px;
    }
    .labelContainer{
        line-height:35px;
    }
    .spinner-border{
        width:1.5rem;
        height:1.5rem;
    }
</style>