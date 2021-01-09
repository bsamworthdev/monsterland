<template>
    <modal @close="close">
        <div slot="header">
            <button type="button" class="close" @click="$emit('close')" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title">Create Monster</h5>
        </div>

        <div slot="body">
            <form action="/createNewMonster" method="POST" class="form-horizontal">
                    <div class="input-group mb-3">
                        <input id="monsterName" type="text" name="name" maxlength="20" class="form-control" v-model="monsterName" placeholder="Enter a name..." value="">
                        <div class="input-group-append">
                            <button class="btn btn-success" @click="setRandomName" type="button">Random!!</button>
                        </div>
                    </div>             
                <div class="form-group"> 
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
                <div class="form-group">
                    <div class="custom-control custom-switch mb-2" :title="nsfwTooltip">
                        <input type="checkbox" name="nsfw" class="custom-control-input" id="nsfw" :disabled="!user_allows_nsfw">
                        <label class="custom-control-label" for="nsfw" :disabled="!user_allows_nsfw">
                            NSFW
                            <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="right" title="Not Safe For Work. (i.e. For adults only)"></i>
                        </label>
                    </div>
                </div>
                <div class="form-group"> 
                    <button id="createMonster" type="submit" class="btn btn-success form-control" :disabled="monsterName == ''">
                        Create Monster
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
                var prefix = this.rand(this.randomWords.prefix);
                var suffix = this.rand(this.randomWords.suffix);
                name = prefix + ' ' + adjective + ' ' + noun  + ' ' + suffix;

                if (name.length > 20){ 
                    var num = Math.random();
                    if (num < 0.8){
                        name = adjective + ' ' + noun;
                    } else if (num < 0.9){
                        name = prefix + ' ' + adjective + ' ' + noun;
                    } else{
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
        float:left;
        padding-bottom:5px;
    }
    #sendButton{
        margin-top:10px;
    }
    .form-group h5{
        font-size:0.85rem;
    }
    .form-group small{
        font-size:0.7rem;
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
</style>