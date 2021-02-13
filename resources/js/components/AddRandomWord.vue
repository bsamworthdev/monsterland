<template>
    <modal @close="close">
        <template v-slot:header>
            <h5 class="modal-title">Add {{ capitaliseFirstLetter(type) }}</h5>
            <button type="button" class="close" @click="$emit('close')" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </template>

        <template v-slot:body>
            <form action="/randomwords/create" method="POST" class="form-horizontal">
                <div class="form-group">
                    <input id="wordText" type="text" name="word" maxlength="20" class="form-control" v-model="wordText" placeholder="Enter word...">
                    <input id="wordType" type="hidden" name="type" :value="type">
                </div>
                <div class="form-group"> 
                    <button id="createWord" type="submit" class="btn btn-success form-control" :disabled="wordText == ''">
                        Add
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
            type: String
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
            document.getElementById('wordText').focus();
            $(function () {
                $('[data-toggle="tooltip"]').tooltip()
            })
        },
        methods: { 
            capitaliseFirstLetter: function(s) {
                if (typeof s !== 'string') return ''
                return s.charAt(0).toUpperCase() + s.slice(1)
            }, 
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