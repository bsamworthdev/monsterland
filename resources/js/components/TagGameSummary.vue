<template>
    <modal @close="close">
        <template v-slot:header>
            <h5 class="modal-title">Thanks for playing</h5>
            <button type="button" class="close" @click="$emit('close')" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </template>

        <template v-slot:body>
             <div v-if="canWinPrizes && awardMessage!=''" class="alert alert-warning text-center mb-1">
                <i class="fas fa-star"></i>
                Congratulations: You won {{ awardMessage }}
                <i class="fas fa-star"></i>
            </div>
            <h1 class="text-center"> 
                You scored {{ pointsCount }}
            </h1>
            <h2 v-show="recordBroken" class="text-gold text-center">
                <div class="alert bg-dark">
                    <i class="fa fa-star"></i>
                    {{ newRecordMessage }} 
                    <i class="fa fa-star"></i>
                </div>
            </h2>
            <div class="container">
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="button" class="btn btn-success btn-block" @click="restart()">Play Again!</button>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="button" class="btn btn-info btn-block" @click="restart(false)">
                            Free-play Mode
                            <br/>
                            <small>Just for fun- No timer</small>
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <template v-slot:footer>
            <button type="button" class="btn btn-default" @click="close()">Close</button>
        </template>
    </modal>
</template>

<script>
    import modal from './Modal' ;

    export default {
        props: {
            pointsCount: Number,
            recordBroken: String,
            loggedIn: Number,
            isPatron: Number,
            hasUsedApp: Number
        },
        components: {
            modal
        },
        data() {
            return {
                saveInProgress: false,
            }
        },
        mounted() {
            console.log('Component mounted.');
        },
        methods: { 
            close: function() {
                this.$emit('close');
            },
            restart: function(allow_timer = true) {
                if (allow_timer){
                    this.$emit('restart');
                } else {
                    this.$emit('restartFreePlay');
                }
            },
        },
        computed: {
            newRecordMessage: function(){
                var message = '';
                switch (this.recordBroken){
                    case 'personal':
                        message = 'NEW PERSONAL RECORD!!';
                        break;
                    case 'all_today':
                        message = 'NEW RECORD TODAY!!';
                        break;
                    case 'all_ever':
                        message = 'NEW ALL TIME RECORD!!';
                        break;
                }
                return message;
            },
            awardMessage: function(){
                var message = '';
                if (this.pointsCount >= 100){
                    message = '5 peeks and 5 redraws';
                } else if (this.pointsCount >= 50){
                    message = '2 peeks and 2 redraws';
                } else if (this.pointsCount >= 30){
                    message = '1 peek and 1 redraw';
                } else if (this.pointsCount >= 10){
                    message = '1 peek';
                }
                return message;
            },
            canWinPrizes: function(){
                return (this.loggedIn && !this.isPatron && !this.hasUsedApp);
            }
            
        }
    }
</script>
<style scoped>
   .text-gold{
       color:gold;
   }
</style>