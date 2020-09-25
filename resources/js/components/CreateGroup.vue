<template>
    <modal @close="close">
        <div slot="header">
            <button type="button" class="close" @click="$emit('close')" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title">Create Group</h5>
        </div>

        <div slot="body">
            <form action="/privategroups/create" method="POST" class="form-horizontal">
                <div class="form-group">
                    <input id="groupName" type="text" name="name" maxlength="20" class="form-control" v-model="groupName" placeholder="Enter a name..." value="">
                </div>
                <div class="form-group"> 
                    <button id="createGroup" type="submit" class="btn btn-success form-control" :disabled="groupName == ''">
                        Create Group
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
        },
        components: {
            modal
        },
        data() {
            return {
                groupName:''
            }
        },
        mounted() {
            console.log('Component mounted.');
            document.getElementById('groupName').focus();
            $(function () {
                $('[data-toggle="tooltip"]').tooltip()
            })
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
    #nsfw{
        margin-left:3px!important;
    }
</style>