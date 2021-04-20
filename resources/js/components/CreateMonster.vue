<template>
    <modal @close="close">
        <template v-slot:header>
            <h5 class="modal-title">Create Monster</h5>
            <button type="button" class="close" @click="$emit('close')" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </template>

        <template v-slot:body>
            <form action="/createNewMonster" method="POST" class="form-horizontal">
                <div class="input-group mb-3">
                    <input id="monsterName" type="text" name="name" maxlength="26" class="form-control" v-model="monsterName" placeholder="Enter a name...">
                    <div class="input-group-append">
                        <button class="btn btn-success" @click="setRandomName" type="button">Random!!</button>
                    </div>
                </div>             
                <div class="form-group" v-show="groupName==''"> 
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
                <div class="form-group col-sm-12">
                    <div class="custom-control custom-switch mb-2" :title="nsfwTooltip">
                        <input type="checkbox" name="nsfw" class="custom-control-input" id="nsfw" :disabled="!user_allows_nsfw">
                        <label class="custom-control-label" for="nsfw" :disabled="!user_allows_nsfw">
                            NSFW
                            <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="right" title="Not Safe For Work. (i.e. For adults only)"></i>
                        </label>
                    </div>
                </div>
                <div v-if="user_is_vip" class="form-group col-sm-12">
                    <div class="custom-control custom-switch mb-2" title="Prevent other artists from peeking">
                        <input type="checkbox" name="prevent_peek" class="custom-control-input" id="prevent_peek">
                        <label class="custom-control-label" for="prevent_peek">
                            Prevent peeking
                        </label>
                    </div>
                </div>
                <div class="form-group"> 
                    <button id="createMonster" type="submit" class="btn btn-success form-control" :disabled="monsterName == ''">
                        Create Monster
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
            groupName: String,
            user_allows_nsfw: Number,
            user_is_vip: Number,
            randomWords: Object
        },
        components: {
            modal
        },
        data() {
            return {
                monsterName:'',
            }
        },
        mounted() {
            console.log('Component mounted.');
            document.getElementById('monsterName').focus();
            $(function () {
                $('[data-toggle="tooltip"]').tooltip()
            })
        },
        methods: { 
            close: function() {
                this.$emit('close')
            },
            setRandomName:function(){
                var name = this.generateRandomName();
                this.monsterName = this.capitaliseFirstLetter(name);
            },
            generateRandomName: function(){
                var name;
                var adjective = this.rand(this.randomWords.adjective);
                var noun = this.rand(this.randomWords.noun);
                var noun2 = this.rand(this.randomWords.noun).toLowerCase();
                var prefix = this.rand(this.randomWords.prefix);
                var suffix = this.rand(this.randomWords.suffix);
                var pronoun = this.rand(['in','inside','under','with','made by', 'stuffed with']);
                name = prefix + ' ' + adjective + ' ' + noun  + ' ' + suffix;

                if (name.length > 26){ 
                    var num = Math.random();
                    if (num < 0.5) {
                        name = adjective + ' ' + noun;
                    } else if (num < 0.8) {
                        //Combine the nouns
                        var startsWithVowel = ['a','e','i','o','u'].indexOf(noun2.charAt(0).toLowerCase()) > -1;
                        var endsInS = noun2.charAt(noun2.length-1) == 's';
                        name =   noun + ' ' + pronoun + (!endsInS ? ' a' + (startsWithVowel ? 'n' : '') : '') + ' ' + noun2;
                    }else if (num < 0.9) {
                        name = prefix + ' ' + adjective + ' ' + noun;
                    } else {
                        name = noun + ' ' + suffix;
                    }
                }

                if (name.length > 20){ 
                    //try again
                    name = this.generateRandomName();
                }

                return name;
            },
            rand: function(items){
                return items[Math.floor(Math.random() * items.length)];
            },
            capitaliseFirstLetter: function(s) {
                if (typeof s !== 'string') return ''
                return s.charAt(0).toUpperCase() + s.slice(1)
            }    
        },
        computed: {
            nsfwTooltip: function(){
                if (this.user_allows_nsfw){
                    return '';
                } else{
                    return 'Enable NSFW switch via https://monsterland.net/settings';
                }
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
        padding-bottom:5px;
        min-height:20px;
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
    .custom-switch input, .custom-switch label{
        cursor:pointer!important;
    }
</style>