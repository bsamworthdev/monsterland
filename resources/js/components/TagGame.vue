<template>
    <div class="mt-1 ml-sm-3 mr-sm-3" :class="{'highlightGreen':wordMatched && !wordBanned}">
        <div class="alert alert-info">
            <div class="container">
                <div class="row">
                    <div class="col-12 col-lg-6">
                        <h5>
                        Enter a word that describes this monster!!
                        </h5>
                        <p>If anyone has entered it before, you'll get a point</p>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="alert alert-light pl-0 pr-0">
                            <div class="container">
                                <div class="row">
                                    <div class="col-12 h5 text-center">
                                        High Scores
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-4">
                                        All Time
                                    </div>
                                    <div class="col-5">
                                        {{ topScoreEver ? topScoreEver.user_name : '' }}
                                    </div>
                                    <div class="col-3">
                                        {{ topScoreEver ? topScoreEver.score : 0 }}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-4">
                                        Today
                                    </div>
                                    <div class="col-5">
                                        {{ topScoreToday ? topScoreToday.user_name : '' }}
                                    </div>
                                    <div class="col-3">
                                        {{ topScoreToday ? topScoreToday.score : 0 }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <div class="container">
                <div class="row">
                    <div class="col-12 col-sm-6 p-0 mb-3" id="image_container">
                        <img v-if="!imageIsLoading" id="monsterImage" class="noshare" :src="'/storage/' + getMonsterId() + '.png'">
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="container">
                            <div class="row">
                                <div class="col-6 pr-0 pl-0">
                                    <h4>
                                        Points: {{ pointsCount }}
                                    </h4>
                                    <h5 v-if="userName">
                                        (Your Best: {{ topScoreUserEver ? topScoreUserEver.score : 0 }})
                                    </h5>
                                </div>
                                <div class="col-6 text-right pr-0 pl-0">
                                    <div id="timerCounter" class="rounded-circle" :class="{'low': timeIsLow}">
                                        {{ timerCount }}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 pb-1">
                                    <div id="message" :class="[ showMessage ? 'visible' : 'invisible', ((wordMatched && !wordBanned) ? 'text-success' : 'text-danger') ]" class="text">
                                        {{ lastEnteredText }} {{ wordMatched ? 'matched' : (wordBanned ? 'is a banned word' : 'not matched') }}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="input-group mb-3">
                                        <input type="text" id="tagName" :disabled="timerCount==0" v-model="enteredText" class="form-control" @keydown="keydown" @keyup="keyup">
                                        <div class="input-group-append">
                                            <button :disabled="enteredText==''" class="btn btn-success w-100" title="Submit" @click="submitWord">
                                                Submit
                                            </button>  
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="timerCount" class="row mt-2 mb-2">
                                <div class="col-12">
                                    <button class="btn btn-info w-100" title="Skip" @click="skipMonster">
                                        Skip <i class="fa fa-arrow-right"></i>
                                    </button> 
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div :class="currentMonster.tags.length ? 'visible' : 'invisible'">
                                        <b class="float-left mr-1">Banned Words: </b>
                                        <div v-for="tag in currentMonster.tags" :key="tag" class="alert alert-info pt-0 pb-0 pl-2 pr-2 mr-1 float-left">
                                            {{ tag.name }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-12">
                                    <div :class="failedWords.length ? 'visible' : 'invisible'">
                                        <b class="float-left mr-1">Guesses: </b>
                                        <div v-for="word in failedWords" :key="word" class="alert alert-danger mr-1 pt-0 pb-0 pl-2 pr-2 float-left">
                                            {{ word }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="timerCount==0" class="row mt-1">
                                <div class="col-12">
                                    <button class="btn btn-success btn-lg w-100" title="Restart Game" @click="restart">
                                        Play Again!
                                    </button> 
                                </div>
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
            :points-count="pointsCount"
            :record-broken="recordBroken">
        </tag-game-summary-component>
        <div v-if="activeModal > 0" class="modal-backdrop fade show"></div>
    </div>         
</template>

<script>
    import tagGameSummaryComponent from './TagGameSummary';
    export default {
        props: {
            userName: String,
            monsters: Object,
            topScores: Object
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
                wordBanned: false,
                showMessage: false,
                pointsCount:0,
                activeModal:0,
                imageIsLoading: false,
                topScoreEver: this.topScores.everyone_ever,
                topScoreToday: this.topScores.everyone_today,
                topScoreUserEver: this.topScores.user_ever,
                recordBroken: '',
                timerPaused: false
            }
        },
        mounted() {
            console.log('Component mounted.');
            this.focusOnTag();
            this.startTimer();
        },
        methods: { 
            restart: function(){
                this.goToNextMonster(true);
                this.pointsCount = 0;
                this.activeModal = 0;
                this.startTimer();
            },
            startTimer: function(){
                setTimeout(() => this.decrementTimer(), 1000);
            },
            pauseTimer: function(){
                this.timerPaused = true;
            },
            unpauseTimer: function(){
                this.timerPaused = false;
            },
            close: function(){
                this.activeModal = 0;
                this.enteredText = '';
            },
            getMonsterId: function(){
                return this.currentMonster.id;
            },
            goToNextMonster: function(resetTimer){
                this.monsterIndex ++;
                this.currentMonster = this.monsters[(this.monsterIndex % this.monsters.length)];
                this.enteredText = '';
                this.failedWords = [];
                if (resetTimer) {
                    this.timerCount = 30;
                    this.timeIsLow = false;
                }
                this.showMessage = false;
                this.imageIsLoading = false;
                this.wordMatched = false;
                this.wordBanned = false;
                this.focusOnTag();
            },
            focusOnTag: function(){
                setTimeout(function(){
                    $('#tagName').focus();
                },100);
            },
            incrementPoints: function(){
                this.pointsCount++;
            },
            skipMonster: function(){
                axios.post('/taggame/saveskip',{
                    action: 'saveskip',
                    monster_id: this.currentMonster.id    
                })
                .then((response) => {
                    this.goToNextMonster(false);
                    console.log(response); 
                })
                .catch((error) => {
                    console.log(error);
                });   
            },
            decrementTimer: function(){

                if (this.timerPaused) {
                    setTimeout(() => this.decrementTimer(), 100);
                    return;
                }
                
                this.timerCount--;
                if (this.timerCount < 11){
                    this.timeIsLow = true;
                }

                if (this.timerCount > 0){
                    setTimeout(() => this.decrementTimer(), 1000);
                } else {
                    console.log('game over');
                    this.setRecordMessage();
                    this.activeModal=1;
                    this.saveScore();
                    // alert('game over');
                }
            },
            setRecordMessage: function(){

                var recordBroken = '';

                if (this.userName == '') {
                    this.recordBroken = '';
                    return;
                }

                if (this.topScoreUserEver && this.pointsCount > this.topScoreUserEver.score){
                    this.topScoreUserEver.score = this.pointsCount;
                    recordBroken = 'personal';
                }
                if (this.topScoreToday && this.pointsCount > this.topScoreToday.score){
                    this.topScoreToday.score = this.pointsCount;
                    this.topScoreToday.user_name = this.userName;
                    recordBroken = 'all_today';
                } 
                if (this.topScoreEver && this.pointsCount > this.topScoreEver.score){
                    this.topScoreEver.score = this.pointsCount;
                    this.topScoreEver.user_name = this.userName;
                    recordBroken = 'all_ever';
                } 

                this.recordBroken = recordBroken;
            },
            saveScore: function(){
                axios.post('/taggame/savescore',{
                    action: 'savescore', 
                    score: this.pointsCount
                })
                .then((response) => {
                    if (!this.topScoreUserEver && this.userName != '') location.reload();
                    console.log(response); 
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            isBannedTag: function(name){
                var bannedTags = this.currentMonster.tags;
                for(var j = 0; j < bannedTags.length; j++){
                    if (bannedTags[j].name == name){
                        return true;
                    }
                }
                return false;
            },
            submitWord: function(){
                var result = 'fail';
                var text= this.enteredText.toLowerCase();
                var tagSubmissions = this.currentMonster.tag_submissions ? this.currentMonster.tag_submissions : [];
                
                this.pauseTimer();
                if (tagSubmissions.length > 0){
                    var approvableSubmissionsCount = 0;
                    for(var i = 0; i < tagSubmissions.length; i++){
                        if (!this.isBannedTag(tagSubmissions[i].name)){
                            approvableSubmissionsCount++;
                        }
                        if (tagSubmissions[i].name == text){
                            if (this.isBannedTag(text)){
                                result = 'banned';
                            } else {
                                result = 'success';
                            }
                        }
                    }
                    //There are none to approve so accept anything
                    if (approvableSubmissionsCount == 0){
                        result = 'success';
                    }
                } else {
                    result = 'success';
                }

                this.lastEnteredText = text;
                if (result == 'fail'){
                    this.wordMatched = false;
                    this.wordBanned = false;
                    this.failedWords.push(text);
                    this.enteredText = '';  
                } else if(result == 'banned'){
                    this.wordMatched = false;
                    this.wordBanned = true;
                    this.enteredText = ''; 
                } else {
                    this.wordMatched = true;
                    this.wordBanned = false;
                    this.imageIsLoading = true
                    this.timerCount = 30;
                    this.timeIsLow = false;
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
                        var _this=this;
                        setTimeout(function(){
                            _this.goToNextMonster(true);
                            _this.unpauseTimer();
                        },500);
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
                        this.unpauseTimer();
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
                    if (this.enteredText !=''){
                        this.submitWord();
                    }
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
        font-size:20px;
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
    .highlightGreen{
        background-color:rgb(218, 236, 218);
    }
</style>