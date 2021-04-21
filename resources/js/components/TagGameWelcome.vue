<template>
    <modal @close="close">
        <template v-slot:header>
            <h5 class="modal-title">Welcome to the Monster Tagging Game!</h5>
            <button type="button" class="close" @click="$emit('close')" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </template>

        <template v-slot:body>
            <h5>
                Enter a word that describes the monster!!
            </h5>
            <p>If anyone has entered it before, you'll get a point.</p>
            <div class="alert alert-info" v-if="canWinPrizes">
                <h5>Prizes</h5>
                <ul>
                    <li>10 points = 1 peek</li>
                    <li>30 points = 1 peek and 1 redraw</li>
                    <li>50 points = 2 peeks and 2 redraws</li>
                    <li>100 points = 5 peeks and 5 redraws</li>
                </ul>
            </div>
            <div class="container">
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="button" class="btn btn-success btn-lg btn-block" @click="restart()">Play!</button>
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
            loggedIn: Number,
            isPatron: Number,
            hasUsedApp: Number
        },
        components: {
            modal
        },
        data() {
            return {
                saveInProgress: false
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