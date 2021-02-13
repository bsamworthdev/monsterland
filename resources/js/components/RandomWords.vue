<template>
    <div>
        <div class="card mb-3">
            <div class="card-header">
                <h4>
                    Prefixes
                    <div class="btn btn-success"  @click="showAddWordModal('prefix')">
                        <i class="fa fa-plus"></i> Add New
                    </div>
                </h4>
            </div>
            <div class="card-body bg-light">
                <div class="float-left" v-for="(word, index) in prefixes" :key="index">
                    <div class="btn btn-info mr-1 mb-1" v-show="index < 20 || showAllPrefixes">
                        {{ word }}
                        <i class="fa fa-times pl-1 text-danger" @click="showRemoveWordModal(word, 'prefix')"></i>
                    </div>
                </div>
                <div v-if="prefixes.length > 0 && prefixes.length > 20 " class="w-100 mt-1" >
                    <button class="btn btn-light btn-block" v-if="!showAllPrefixes" @click="toggleShowAll('prefixes')">
                        <i class="fa fa-sort-down"></i>
                        View more...
                    </button>
                    <button class="btn btn-light btn-block" v-if="showAllPrefixes" @click="toggleShowAll('prefixes')">
                        <i class="fa fa-sort-up"></i>
                        View less...
                    </button>
                </div>
            </div>
        </div>

         <div class="card mb-3">
            <div class="card-header">
                <h4>Suffixes
                    <div class="btn btn-success" @click="showAddWordModal('suffix')">
                        <i class="fa fa-plus"></i> Add New
                    </div>
                </h4>
            </div>
            <div class="card-body bg-light">
                <div class="float-left" v-for="(word, index) in suffixes" :key="index">
                    <div class="btn btn-info mr-1 mb-1" v-show="index < 20 || showAllSuffixes">
                        {{ word }}
                        <i class="fa fa-times pl-1 text-danger" @click="showRemoveWordModal(word, 'suffix')"></i>
                    </div>
                </div>
                <div v-if="suffixes.length > 0 && suffixes.length > 20 " class="w-100 mt-1" >
                    <button class="btn btn-light btn-block" v-if="!showAllSuffixes" @click="toggleShowAll('suffixes')">
                        <i class="fa fa-sort-down"></i>
                        View more...
                    </button>
                    <button class="btn btn-light btn-block" v-if="showAllSuffixes" @click="toggleShowAll('suffixes')">
                        <i class="fa fa-sort-up"></i>
                        View less...
                    </button>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h4>Adjectives
                    <div class="btn btn-success" @click="showAddWordModal('adjective')">
                        <i class="fa fa-plus"></i> Add New
                    </div>
                </h4>
            </div>
            <div class="card-body bg-light">
                <div class="float-left" v-for="(word, index) in adjectives" :key="index">
                    <div class="btn btn-info mr-1 mb-1" v-show="index < 20 || showAllAdjectives">
                        {{ word }}
                        <i class="fa fa-times pl-1 text-danger" @click="showRemoveWordModal(word, 'adjective')"></i>
                    </div>
                </div>
                <div v-if="adjectives.length > 0 && adjectives.length > 20 " class="w-100 mt-1" >
                    <button class="btn btn-light btn-block" v-if="!showAllAdjectives" @click="toggleShowAll('adjectives')">
                        <i class="fa fa-sort-down"></i>
                        View more...
                    </button>
                    <button class="btn btn-light btn-block" v-if="showAllAdjectives" @click="toggleShowAll('adjectives')">
                        <i class="fa fa-sort-up"></i>
                        View less...
                    </button>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4>Nouns
                    <div class="btn btn-success"  @click="showAddWordModal('noun')">
                        <i class="fa fa-plus"></i> Add New
                    </div>
                </h4>
            </div>
            <div class="card-body bg-light">
                <div class="float-left" v-for="(word, index) in nouns" :key="index">
                    <div class="btn btn-info mr-1 mb-1" v-show="index < 20 || showAllNouns">
                        {{ word }}
                        <i class="fa fa-times pl-1 text-danger" @click="showRemoveWordModal(word, 'noun')"></i>
                    </div>
                </div>
                <div v-if="nouns.length > 0 && nouns.length > 20 " class="w-100 mt-1" >
                    <button class="btn btn-light btn-block" v-if="!showAllNouns" @click="toggleShowAll('nouns')">
                        <i class="fa fa-sort-down"></i>
                        View more...
                    </button>
                    <button class="btn btn-light btn-block" v-if="showAllNouns" @click="toggleShowAll('nouns')">
                        <i class="fa fa-sort-up"></i>
                        View less...
                    </button>
                </div>
            </div>
        </div>

        <add-random-word-component
            v-if="activeModal==1" 
            :type="selectedType"
            @close="activeModal=0" >
        </add-random-word-component>
        <delete-random-word-component
            v-if="activeModal==2" 
            :type="selectedType"
            :word="selectedWord"
            @close="activeModal=0" >
        </delete-random-word-component>
        <div v-if="activeModal > 0" class="modal-backdrop fade show"></div>
    </div>
</template>

<script>
    import addRandomWordComponent from './AddRandomWord' ;
    import deleteRandomWordComponent from './DeleteRandomWord' ;
    export default {
        props: {
           randomWords: Object
        },
        components: {
            addRandomWordComponent,
            deleteRandomWordComponent
        },
        data() {
            return {
                monsterName:'',
                showAllNouns:false,
                showAllAdjectives:false,
                showAllPrefixes:false,
                showAllSuffixes:false,
                activeModal:0,
                selectedType:'',
                selectedWord:''
            }
        },
        mounted() {
            console.log('Component mounted.');
        },
        computed:{
            prefixes:function(){
                return this.randomWords.prefix;
            },
            suffixes:function(){
                return this.randomWords.suffix;
            },
            adjectives:function(){
                return this.randomWords.adjective;
            },
            nouns:function(){
                return this.randomWords.noun;
            }
        },
        methods: { 
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
            }, 
            toggleShowAll:function(type){
                switch (type){
                    case 'nouns': 
                        this.showAllNouns=!this.showAllNouns;
                        break;
                    case 'adjectives': 
                        this.showAllAdjectives=!this.showAllAdjectives;
                        break;
                    case 'prefixes': 
                        this.showAllPrefixes=!this.showAllPrefixes;
                        break;
                    case 'suffixes': 
                        this.showAllSuffixes=!this.showAllSuffixes;
                        break;
                }
            },
            showAddWordModal:function(type){
                this.selectedType=type;
                this.activeModal=1;
            },
            showRemoveWordModal:function(word, type){
                this.selectedType=type;
                this.selectedWord=word;
                this.activeModal=2;
            },
            close: function() {
                this.$emit('close')
            }
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