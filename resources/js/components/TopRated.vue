<template>
    <div class="container">
        <div class="row justify-content-center">
            <div id="main-container" class="col-md-12">

                <div class="container">
                    <div class="row mb-2">
                        <div class="col-lg-3 col-4 mt-1">
                            <h3 class="text-right">
                                Top rated
                            </h3> 
                        </div>
                        <div class="col-lg-3 col-8 mt-1">
                            <select id="timeFilter" v-model="selectedTimeFilter" class="form-control" @change="timeFilterChanged($event)">
                                <option value="day">Day</option>
                                <option value="week">Week</option>
                                <option value="month">Month</option>
                                <option value="year">Year</option> 
                                <option value="ever">All Time</option> 
                            </select>
                        </div>
                        <div class="col-lg-2 col-4 mt-1">
                            <h5 class="text-right">
                                Search
                            </h5> 
                        </div>
                        <div class="col-lg-3 col-6 mt-1">
                           <input id="searchText" class="form-control" type="text" v-model="enteredSearchText" @keydown="searchKeyDown" />
                        </div>
                        <div class="col-lg-1 col-2 mt-1 pull-left pl-0">
                            <button class="btn btn-success btn-block pl-0 pr-0" @click="searchClick">
                                <i class="fas fa-arrow-right"></i>
                            </button>
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
                    <div v-if="monsters.length>0" class="card mb-3">
                        <div class="container">
                            <div class="row">
                                <div v-for="(monster, index) in monsters" class="monster col-lg-3 col-6" :key="monster.id">
                                    <monster-thumbnail-component
                                        :user="userJSON"
                                        :monster="monster"
                                        :monster-sequence-num="(page * 8) + index"
                                        :time-filter="timeFilter"
                                        :search="search"
                                        :page-type="pageType"
                                        :is-my-page="isMyPage"
                                        :group-id="groupId">
                                    </monster-thumbnail-component>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="row">
                        <h3 class="pl-2"><i>No monsters here!</i></h3>
                    </div>
                    <div class="row mb-2 d-lg-none">
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
                </div>

            </div>
        </div>
    </div>
</template>

<script>
    import monsterThumbnailComponent from './MonsterThumbnail' ;
    export default {
        props: {
            user: {
                default: null,
                format: Object
            },
            monsters: Array,
            page: Number,
            timeFilter: String,
            path: String,
            search: {
                default:'',
                format: String
            },
            pageType: String,
            isMyPage:{
                default: null,
                format: Number
            },
            groupId: Number
        },
        components: {
            monsterThumbnailComponent
        },
        methods: {
            prevClick: function() {
                var page = this.page - 1;
                location.href = '/' + this.path + '/' + page + '/' + this.timeFilter + (this.enteredSearchText ? ('/' + this.enteredSearchText) : '');
            },
            nextClick: function() {
                var page = this.page + 1;
                location.href = '/' + this.path + '/' + page + '/' + this.timeFilter + (this.enteredSearchText ? ('/' + this.enteredSearchText) : '');
            },
            timeFilterChanged: function(event) {
                location.href = '/' + this.path + '/0/' + event.target.value + (this.enteredSearchText ? ('/' + this.enteredSearchText) : '');
                event.stopPropagation();
            },
            searchClick: function(event) {
                location.href = '/' + this.path + '/' + this.page + '/' + this.timeFilter + (this.enteredSearchText ? ('/' + this.enteredSearchText) : '');
            },
            searchKeyDown: function(event) { 
                if (event.keyCode === 13) { //enter
                    location.href = '/' + this.path + '/' + this.page + '/' + this.timeFilter + (this.enteredSearchText ? ('/' + this.enteredSearchText) : '');
                }
            },
        },
        computed: {
            lockPrev: function(){
                return this.page == 0
            },
            lockNext: function(){
                return this.monsters.length != 8;
            },
            lockSearch: function(){
                return this.enteredSearchText.length == 0;
            },
            userJSON: function(){
                if (this.user) {
                    return JSON.parse(this.user);
                }
                else {
                    return null;
                }
            }
        },
        data() {
            return {
                selectedTimeFilter : this.timeFilter,
                enteredSearchText : this.search
            }
        },
        mounted() {
            this.selectedTimeFilter = this.timeFilter;
            this.enteredSearchText = this.search;
            console.log('Component mounted.')
        }
    }
</script>

<style scoped>
    h3{
        font-size: 14px;
        line-height: 30px;
        margin-bottom: 0px;

    } 
    .monster{
        padding:0!important;
    }
    @media (min-width: 768px) 
    {
         h3{
            font-size: 18px;
        }
    }
</style>
