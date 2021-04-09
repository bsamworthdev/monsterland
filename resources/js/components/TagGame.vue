<template>
    <div class="mt-1 ml-5 mr-5">
        <div class="alert alert-info">
            Enter a word that describes this monster!!
        </div>
        <div>
            <div class="container">
                <div class="row">
                    <div class="col-6 p-0" id="image_container">
                        <img v-if="!imageIsLoading" id="monsterImage" class="noshare" :src="'/storage/' + getMonsterId() + '.png'">
                    </div>
                    <div class="col-6">
                        <div class="container">
                            <div class="row">
                                <div class="col-6 pr-0 pl-0">
                                    <h4>
                                        Points: {{ pointsCount }}
                                    </h4>
                                </div>
                                <div class="col-6 text-right pr-0 pl-0">
                                    <div id="timerCounter" class="rounded-circle" :class="{'low': timeIsLow}">
                                        {{ timerCount }}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div id="message" :class="[ showMessage ? 'visible' : 'invisible', wordMatched ? 'text-success' : 'text-danger' ]" class="text">
                                    {{ lastEnteredText }} {{ wordMatched ? 'matched' : 'not matched' }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-group mb-3">
                                    <input type="text" v-model="enteredText" class="form-control" @keydown="keydown" @keyup="keyup">
                                    <div class="input-group-append">
                                        <button :disabled="enteredText==''" class="btn btn-success w-100" title="Submit" @click="submitWord">
                                            Submit
                                        </button>  
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div :class="currentMonster.tags.length ? 'visible' : 'invisible'">
                                        <b class="float-left">Banned Words: </b>
                                        <div v-for="tag in currentMonster.tags" :key="tag" class="alert alert-info pt-0 pb-0 pl-2 pr-2 mr-1 float-left">
                                            {{ tag.name }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div :class="currentMonster.tags.length ? 'visible' : 'invisible'">
                                    <b class="float-left">Guesses: </b>
                                    <div v-for="word in failedWords" :key="word" class="alert alert-danger mr-1 pt-0 pb-0 pl-2 pr-2 float-left">
                                        {{ word }}
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-4" style="position:absolute; bottom:40px; width:100%">
                                <button class="btn btn-info w-100" title="Skip" @click="skipMonster">
                                    Skip <i class="fa fa-arrow-right"></i>
                                </button> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <tag-game-summary-component
            v-if="activeModal==1" 
            @close="activeModal=0"
            @restart="restart"
            :points-count="pointsCount">
        </tag-game-summary-component>
        <div v-if="activeModal > 0" class="modal-backdrop fade show"></div>
    </div>         
</template>

<script>
    import tagGameSummaryComponent from './TagGameSummary';
    export default {
        props: {
            monsters: Object
        },
        components : {
            tagGameSummaryComponent
        },
        data() {
            return {
                enteredText:'',
                lastEnteredText: '',
                timerCount: 30,
                timeIsLow:false,
                monsterIndex:0,
                currentMonster: this.monsters[0],
                failedWords: [],
                wordMatched: false,
                showMessage: false,
                pointsCount:0,
                activeModal:0
            }
        },
        mounted() {
            console.log('Component mounted.');
            setTimeout(() => this.decrementTimer(), 1000);
        },
        methods: { 
            restart: function(){
                this.goToNextMonster(true);
                this.pointsCount = 0;
                this.activeModal = 0;
            },
            close: function(){
                this.activeModal = 0;
            },
            getMonsterId: function(){
                return this.currentMonster.id;
            },
            goToNextMonster: function(resetTimer){
                this.monsterIndex ++;
                this.currentMonster = this.monsters[(this.monsterIndex % this.monsters.length)];
                this.enteredText = '';
                this.failedWords = [];
                if (resetTimer) this.timerCount = 30;
                this.showMessage = false;
                this.imageIsLoading = false;

            },
            incrementPoints: function(){
                this.pointsCount++;
            },
            skipMonster: function(){
                this.goToNextMonster(false);
            },
            decrementTimer: function(){

                this.timerCount--;
                if (this.timerCount < 11){
                    this.timeIsLow = true;
                }

                if (this.timerCount > 0){
                    setTimeout(() => this.decrementTimer(), 1000);
                } else {
                    console.log('game over');
                    this.activeModal=1;
                    // alert('game over');
                }
            },
            isBannedTag: function(name){
                var bannedTags = this.currentMonster.tags;
                for(var j = 0; j < bannedTags.length; j++){
                    if (bannedTags[j] == (this.enteredText)){
                        return true;
                    }
                }
                return false;
            },
            submitWord: function(){
                var result = 'fail';
                var tagSubmissions = this.currentMonster.tag_submissions ? this.currentMonster.tag_submissions : [];
                if (tagSubmissions.length > 0){
                    for(var i = 0; i < tagSubmissions.length; i++){
                        if (tagSubmissions[i].name == this.enteredText && !this.isBannedTag(this.enteredText)){
                            result = 'success';
                            break;
                        }
                    }

                } else {
                    result = 'success';
                }

                this.lastEnteredText = this.enteredText;
                if (result == 'fail'){
                    this.wordMatched = false;
                    this.failedWords.push(this.enteredText);
                    this.enteredText = '';  
                } else {
                    this.wordMatched = true;
                    this.imageIsLoading = true
                    this.incrementPoints();
                }
                this.showMessage = true;

                //TODO refactor to prevent duplication
                if (result == 'success'){
                    axios.post('/taggame/savesubmission',{
                        action: 'savesubmission',
                        monster_id: this.currentMonster.id,  
                        name: this.lastEnteredText      
                    })
                    .then((response) => {
                        this.goToNextMonster(true);
                        console.log(response); 
                    })
                    .catch((error) => {
                        console.log(error);
                    });    
                } else {
                    axios.post('/taggame/savesubmission',{
                        action: 'savesubmission',
                        monster_id: this.currentMonster.id,  
                        name: this.lastEnteredText      
                    })
                    .then((response) => {
                        console.log(response); 
                    })
                    .catch((error) => {
                        console.log(error);
                    });
                }
                

                
            },
            keydown: function(){
                this.showMessage = false;
            },
            keyup: function(e){
                //enter
                if (e.keyCode === 13) {
                    this.submitWord();
                }
                
            }
        }
    }
</script>
<style scoped>
    #image_container{
        width:100%;
        background-color:#FFF;
        border:1px solid black;
        box-shadow:-4px 4px #cacaca!important;
    }
    #image_container:after {
        content: "";
        display: block;
        padding-bottom: 100%;
    }
    #monsterImage{
        width:100%;
        position: absolute;
        height: 100%;
    }
    #message{
        clear:both;
    }
    #timerCounter{
        width:50px;
        color:white;
        background-color:darkgray;
        text-align:center;
        font-size:2.0em;
        margin-left:auto;
    }
    #timerCounter.low{
        color:white;
        background-color:red;
    }
</style>