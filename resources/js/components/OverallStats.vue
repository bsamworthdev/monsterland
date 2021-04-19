<template>
    <div id="app">
        <div class="container">
            <div class="row">
                <label>Monsters Created: {{ overallStats.monster_count }}</label>
            </div>
            <div class="row mb-3">
                <label>Monsters with one or more tags:</label>
                <div class="progress w-100">
                    <div class="progress-bar" role="progressbar" :style="'width: ' + overallStats.tagged_percent + '%;'" :aria-valuenow="overallStats.tagged_percent" aria-valuemin="0" aria-valuemax="100">{{ overallStats.tagged_percent }}%</div>
                </div>
            </div>
            <div class="row">
                <label>Monsters with completed tags:</label>
                <div class="progress w-100">
                    <div class="progress-bar" role="progressbar" :style="'width: ' + overallStats.fully_tagged_percent + '%;'" :aria-valuenow="overallStats.fully_tagged_percent" aria-valuemin="0" aria-valuemax="100">{{ overallStats.fully_tagged_percent }}%</div>
                </div>
            </div>
            
        </div>
    </div>
</template>

<script>
    export default {
        props: {
          overallStats: Object
        },
        methods: {
            
        },
        computed: {
           
        },
        mounted() {
            console.log('Component mounted.')
            
           
        },
    }
</script>

<style scoped>


</style>
