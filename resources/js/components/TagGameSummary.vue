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
            pointsCount: Number
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
        }
    }
</script>
<style scoped>
   
</style>