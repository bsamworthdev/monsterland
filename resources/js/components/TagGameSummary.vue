<template>
    <modal @close="close">
        <template v-slot:header>
            <h5 class="modal-title">Thanks for playing</h5>
            <button type="button" class="close" @click="$emit('close')" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </template>

        <template v-slot:body>
            <h3> 
                You scored {{ pointsCount }}
            </h3>
            <h2 v-show="recordBroken" class="text-gold text-center">
                <div class="alert bg-dark">
                    <i class="fa fa-star"></i>
                    {{ newRecordMessage }} 
                    <i class="fa fa-star"></i>
                </div>
            </h2>
            <button type="button" class="btn btn-success btn-block mt-3" @click="restart()">Play Again!</button>
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
            recordBroken: String
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
            restart: function() {
                this.$emit('restart');
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
            }
            
        }
    }
</script>
<style scoped>
   .text-gold{
       color:gold;
   }
</style>