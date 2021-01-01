<template>
    <modal @close="close">
        <div slot="header">
            <button type="button" class="close" @click="$emit('close')" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title">Delete Word</h5>
        </div>

        <div slot="body">
            <form action="/randomwords/delete" method="POST" class="form-horizontal">
                <p>
                    Delete the following {{ type }}:
                </p>
                <h5 class="mb-3">
                    {{ word }}
                </h5>
                <div class="form-group">
                    <input id="wordText" type="hidden" name="word" :value="word">
                    <input id="wordType" type="hidden" name="type" :value="type">
                </div>
                <div class="form-group"> 
                    <button id="createGroup" type="submit" class="btn btn-danger form-control">
                        Delete
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
            word: String,
            type: String
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