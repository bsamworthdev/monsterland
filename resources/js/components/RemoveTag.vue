<template>
    <modal @close="close">
        <template v-slot:header>
            <h5 class="modal-title">Remove Tag</h5>
            <button type="button" class="close" @click="$emit('close')" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </template>

        <template v-slot:body>
            <form method="POST" class="form-horizontal">
                <div class="form-group">
                    <p>Are you sure you want to remove this tag for <b>{{ monster.name }}</b> ?</p>
                </div>
                <div class="form-group"> 
                    <button id="removeTagButton" :disabled="removingInProgress" type="button" @click="removeTag" class="btn btn-danger form-control">
                        
                        <div class="spinner-border" v-if="removingInProgress" role="status">
                            <span class="sr-only"> Removing...</span>
                        </div>
                        <span v-else>
                            Remove Tag
                        </span>
                    </button>

                </div>    
            </form>
        </template>

        <template v-slot:footer>
            <button type="button" class="btn btn-default" @click="close">Close</button>
        </template>
    </modal>
</template>

<script>
    import modal from './Modal' ;

    export default {
        props: {
            monster: Object,
            removingInProgress: Number
        },
        components: {
            modal
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
            removeTag: function() {
                this.$emit('remove');
            }
        }
    }
</script>
<style scoped> 
    #removeTagButton{
        margin-top:10px;
    }
    .btn-info:not(.active){
        background-color:#DDEDFA!important;
    }
    .btn-info:not(.active):hover{
        color:#C0C0C0;
    }
</style>