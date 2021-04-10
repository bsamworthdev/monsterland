<template>
    <modal @close="close">
        <template v-slot:header>
            <h5 class="modal-title">Add Tag</h5>
            <button type="button" class="close" @click="$emit('close')" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </template>

        <template v-slot:body>
            <form method="POST" class="form-horizontal">
                <div class="form-group">
                    <p>New tag for <b>{{ monster.name }}</b>: </p>
                    <input type="text" id="tagName" class="form-control" name="tagName" placeholder="Enter tag here..." v-model="enteredTagName"/>
                </div>
                <div class="form-group"> 
                    <button id="addTagButton" :disabled="addingInProgress" type="button" @click="addTag" class="btn btn-success form-control">
                        
                        <div class="spinner-border" v-if="addingInProgress" role="status">
                            <span class="sr-only"> Adding...</span>
                        </div>
                        <span v-else>
                            Add Tag
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
            addingInProgress: Number,
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
        data() {
            return {
                enteredTagName: ''
            }
        },
        methods: { 
            close: function() {
                this.$emit('close');
            },
            addTag: function() {
                this.$emit('add', this.enteredTagName);
            }
        }
    }
</script>
<style scoped> 
    #addTagButton{
        margin-top:10px;
    }
    .btn-info:not(.active){
        background-color:#DDEDFA!important;
    }
    .btn-info:not(.active):hover{
        color:#C0C0C0;
    }
</style>