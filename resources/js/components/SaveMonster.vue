<template>
    <modal @close="close">
        <template v-slot:header>
            <h5 class="modal-title">Save Monster</h5>
            <button type="button" class="close" @click="$emit('close')" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </template>

        <template v-slot:body>
            <form method="POST" class="form-horizontal">
                <div class="form-group">
                    <h4>Are you sure you want to save?</h4>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-switch mb-2" v-show="showEmailToggle()">
                        <input type="checkbox" name="completeEmail" @change="toggleEmailOnComplete()" :checked="email_on_complete" class="custom-control-input" id="completeEmail">
                        <label class="custom-control-label" for="completeEmail">
                            Email me when this monster is finished
                        </label>
                    </div>
                </div>
                <div class="form-group"> 
                    <button id="saveMonster" :disabled="saveInProgress" type="button" @click="save" class="btn btn-success form-control">
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
            segmentName: String,
            email_on_complete: Boolean,
            loggedIn: String
        },
        components: {
            modal
        },
        data() {
            return {
                saveInProgress: false
            }
        },
        mounted() {
            console.log('Component mounted.');
            $(function () {
                $('[data-toggle="tooltip"]').tooltip()
            })
        },
        methods: { 
            showEmailToggle: function(){
                if (this.segmentName != 'legs' && this.loggedIn == "1"){
                    return true;
                }
                return false;
            },
            toggleEmailOnComplete: function(){
                // this.emailOnCompleteValue = !this.emailOnCompleteValue;
                this.$emit('toggleEmailOnComplete');
            },
            close: function() {
                this.$emit('close');
            },
            save: function() {
                this.saveInProgress = true;
                this.$emit('save');
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
    #saveMonster div{
        float:none!important;
    }
    .spinner-border{
        width:1.5rem;
        height:1.5rem;
    }
</style>