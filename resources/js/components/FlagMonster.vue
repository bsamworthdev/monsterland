<template>
    <modal @close="close">
        <template v-slot:header>
            <button type="button" class="close" @click="$emit('close')" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title">Flag Monster</h5>
        </template>

        <template v-slot:body>
            <form method="POST" class="form-horizontal">
                <div class="form-group">
                    <p>Flagging this monster will cause it to be hidden to all users until it has been reviewed by an admin.</p>
                </div>
                <div class="form-group"> 
                    <button id="flagButton" type="button" @click="flag" class="btn btn-danger form-control">
                        I understand. Flag this monster.
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
            flag: function() {
                this.$emit('flag');
            }
        }
    }
</script>
<style scoped> 
    #flagButton{
        margin-top:10px;
    }
    .btn-info:not(.active){
        background-color:#DDEDFA!important;
    }
    .btn-info:not(.active):hover{
        color:#C0C0C0;
    }
</style>