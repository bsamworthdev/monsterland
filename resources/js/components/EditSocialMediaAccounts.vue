<template>
    <modal @close="close">
        <template v-slot:header>
            <h5 class="modal-title">My Accounts</h5>
            <button type="button" class="close" @click="$emit('close')" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </template>

        <template v-slot:body>
            <form action="/mymonsters/saveaccounts" method="POST" class="form-horizontal">
                <div v-for="(account, index) in socialMediaAccounts" :key="index" class="account p-1">
                    <img class="icon" :src="'/images/' + account.account_type + '.png'">
                    <input type="text" class="ml-2" :value="account.account_name">
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
            socialMediaAccounts: Array
        },
        components: {
            modal
        },
        data() {
            return {
                wordText:''
            }
        },
        mounted() {
            console.log('Component mounted.');
        },
        methods: { 

        }
    }
</script>
<style scoped>
    .modal-title{
        color:#000000!important;
    }
    .icon{
        height:30px;
        width:30px;
    }
</style>