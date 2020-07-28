<template>
    <modal @close="close">
        <div slot="header">
            <button type="button" class="close" @click="$emit('close')" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title">Create Monster</h5>
        </div>

        <div slot="body">
            <form action="/createNewMonster" method="POST" class="form-horizontal">
                <div class="form-group">
                    <input id="monsterName" type="text" name="name" class="form-control" placeholder="Enter a name..." value="">
                </div>
                <div class="form-group"> 
                    <div class="btn-group btn-group-toggle float-none d-flex" data-toggle="buttons">
                        <label class="btn btn-info active">
                            <input type="radio" name="level" value="basic" id="basic" autocomplete="off" checked> 
                            <h5>Basic</h5>
                            <small>Open to everyone</small>
                        </label>
                        <label class="btn btn-info">
                            <input type="radio" name="level" value="standard" id="standard" autocomplete="off"> 
                            <h5>Standard</h5>
                            <small>Registered users only</small>
                        </label>
                        <label v-if="user_is_vip" class="btn btn-info">
                            <input type="radio" name="level" value="pro" id="pro" autocomplete="off"> 
                            <h5>Pro</h5>
                            <small>Advanced users only</small>
                        </label>
                    </div>
                </div>
                <div class="form-group"> 
                    <button id="createMonster" type="submit" class="btn btn-success form-control">
                        Create Monster
                    </button>
                </div>    
            </form>
        </div>

        <div slot="footer">
            <button type="button" class="btn btn-default" @click="$emit('close')">Close</button>
        </div>
    </modal>
</template>

<script>
    import modal from './Modal' ;

    export default {
        props: {
           user_is_vip: Number
        },
        components: {
            modal
        },
        mounted() {
            console.log('Component mounted.')
        },
        methods: { 
            close: function() {
                this.$emit('close')
            }
        }
    }
</script>
<style scoped>
    .form-group label {
        clear:both;
        float:left;
        vertical-align: top;
    }
    .form-group div {
        float:left;
        padding-bottom:5px;
    }
    #sendButton{
        margin-top:10px;
    }

    .btn-info:not(.active){
        background-color:#DDEDFA!important;
    }
    .btn-info:not(.active):hover{
        color:#C0C0C0;
    }
</style>