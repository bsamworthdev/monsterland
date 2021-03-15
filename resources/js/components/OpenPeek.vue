<template>
    <modal @close="close">
        <template v-slot:header>
            <h5 class="modal-title">Peek</h5>
            <button type="button" class="close" @click="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </template>

        <template v-slot:body>
            <form method="POST" class="form-horizontal">
                <div class="form-group">
                    <h4>Are you sure you want to peek at the {{ segmentName == 'legs' ? ' body' : 'head' }}?</h4>
                    <small v-if="!user.has_used_app && !user.is_patron">
                        (This will use up one of your limited number of peeks.)
                    </small>
                </div>
                <div class="form-group"> 
                    <button id="peek" type="button" @click="activatePeekMode" class="btn btn-success form-control">
                        Use peek
                    </button>
                </div>    
            </form>
        </template>

        <template v-slot:footer>
            <button type="button" class="btn btn-default" @click="close">Cancel</button>
        </template>
    </modal>
</template>

<script>
    import modal from './Modal' ;

    export default {
        props: {
            user: Object,
            segmentName: String,
            loggedIn: String
        },
        components: {
            modal
        },
        data() {
            return {
            }
        },
        mounted() {
            console.log('Component mounted.');
            $(function () {
                $('[data-toggle="tooltip"]').tooltip()
            })
        },
        methods: { 
            close: function() {
                this.$emit('close');
            },
            activatePeekMode: function() {
                this.$emit('activatePeekMode');
                this.close();
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