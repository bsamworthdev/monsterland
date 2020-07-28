<template>
    <div class="container">
        <div class="row justify-content-center">
            <div id="main-container" class="col-md-12">

                <div class="container">
                    <div class="row mb-2">
                        <div class="col-lg-3 col-6">
                            <h3 v-if="path=='halloffame'" class="text-right mr-2">
                                Top monsters
                            </h3> 
                            <h3 v-else-if="path=='mymonsters'" class="text-right mr-2">
                                My Top monsters
                            </h3> 
                        </div>
                        <div class="col-lg-3 col-6">
                            <select id="timeFilter" v-model="selectedTimeFilter" class="form-control" @change="timeFilterChanged($event)">
                                <option value="day">Day</option>
                                <option value="week">Week</option>
                                <option value="month">Month</option>
                                <option value="year">Year</option> 
                                <option value="ever">All Time</option> 
                            </select>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-6">
                            <button class="btn btn-info btn-block" :disabled="lockPrev" @click="prevClick">
                                <i class="fas fa-arrow-left"></i> <span class="btnLabel">Previous</span>
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-info btn-block" :disabled="lockNext" @click="nextClick">
                                <span class="btnLabel">Next</span> <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card mb-3">
                        <div class="container">
                            <div class="row">
                                <div v-for="monster in monsters" class="monster col-lg-3 col-6" :key="monster.id">
                                    <monster-thumbnail-component
                                        :monster="monster">
                                    </monster-thumbnail-component>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
</template>

<script>
    import monsterThumbnailComponent from './MonsterThumbnail' ;
    export default {
        props: {
            monsters: Array,
            page: Number,
            timeFilter: String,
            path: String
        },
        components: {
            monsterThumbnailComponent
        },
        methods: {
            prevClick: function() {
                var page = this.page - 1;
                location.href = '/' + this.path + '/' + page + '/' + this.timeFilter;
            },
            nextClick: function() {
                var page = this.page + 1;
                location.href = '/' + this.path + '/' + page + '/' + this.timeFilter;
            },
            timeFilterChanged: function(event) {
                location.href = '/' + this.path + '/0/' + event.target.value;
            }
        },
        computed: {
            lockPrev: function(){
                return this.page == 0
            },
            lockNext: function(){
                return this.monsters.length != 8;
            },
        },
        data() {
            return {
                selectedTimeFilter : this.timeFilter
            }
        },
        mounted() {
            this.selectedTimeFilter = this.timeFilter;
            console.log('Component mounted.')
        }
    }
</script>

<style scoped>
    h3{
        font-size: 1.4em;
        line-height: 30px;
        margin-bottom: 0px;

    } 
    .monster{
        padding:0!important;
    }
</style>
