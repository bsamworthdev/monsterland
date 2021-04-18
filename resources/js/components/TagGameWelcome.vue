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
            <p>If anyone has entered it before, you'll get a point</p>
            <div class="container">
                <div class="row mt-3">
                    <div class="col-6">
                        <button type="button" class="btn btn-success btn-block" @click="restart()">Play!</button>
                    </div>
                    <div class="col-6">
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
        }
    }
</script>
<style scoped>
   .text-gold{
       color:gold;
   }
</style>