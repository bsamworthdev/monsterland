<template>
    <modal @close="close">
        <template v-slot:header>
            <h5 class="modal-title">Award weekly trophies</h5>
            <button type="button" class="close" @click="$emit('close')" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </template>

        <template v-slot:body>
            <form action="/awardWeeklyTrophies" method="POST" class="form-horizontal">    
                <div class="form-group mb-2"> 
                    <label for="firstPlace" class="col-sm-2">1st Place</label>
                    <input type="number" name="firstPlace" min="0" class="form-control col-sm-4" id="firstPlace">
                </div>
                <div class="form-group mb-2"> 
                    <label for="secondPlace" class="col-sm-2">2nd Place</label>
                    <input type="number" name="secondPlace" min="0"  class="form-control col-sm-4" id="secondPlace">
                </div>
                <div class="form-group mb-2"> 
                    <label for="thirdPlace" class="col-sm-2">3rd Place</label>
                    <input type="number" name="thirdPlace" min="0"  class="form-control col-sm-4" id="thirdPlace">
                </div>
                <div class="form-group"> 
                    <button id="awardTrophies" type="submit" class="btn btn-success form-control">
                        Award Trophies
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
                monsterName:'',
            }
        },
        methods: { 
            close: function() {
                this.$emit('close')
            },   
        },
        mounted() {
            console.log('Component mounted.');
        },
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
    .form-group h5{
        font-size:0.95rem;
    }
    .form-group small{
        font-size:0.8rem;
    }
    .btn-info:not(.active){
        background-image:none!important;
        background-color:#DDEDFA!important;
    }
    .btn-info:not(.active):hover{
        color:#C0C0C0;
    }
    #nsfw{
        margin-left:3px!important;
    }
    input{
        -webkit-appearance: none;
    }
</style>