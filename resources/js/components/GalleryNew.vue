<template>
    <div class="container">
        <div class="row justify-content-center">
            <div id="main-container" class="col-md-12">

                <div class="container">
                    <div class="row mb-2">
                        <div class="col-lg-3 col-6 mt-1">
                            <select id="sortBy" v-model="selectedSortBy" class="form-control" @change="sortByChanged($event)">
                                <option value="highest_rated">Highest Rated</option>
                                <option value="lowest_rated">Lowest Rated</option>
                                <option value="newest">Newest</option>
                                <option value="oldest">Oldest </option>
                            </select>
                        </div>
                        <div class="col-lg-3 col-6 mt-1">
                            <select id="timeFilter" v-model="selectedTimeFilter" class="form-control" @change="timeFilterChanged($event)">
                                <option value="day">Day</option>
                                <option value="week">Week</option>
                                <option value="month">Month</option>
                                <option value="year">Year</option> 
                                <option value="ever">All Time</option> 
                            </select>
                        </div>
                        <div class="col-lg-6 col-12 mt-1">
                            <div class="form-group has-search">
                                <span class="fa fa-search form-control-feedback"></span>
                                <input id="searchText" placeholder="Search" class="form-control" type="text" v-model="enteredSearchText" @keyup="searchKeyUp" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-3 col-6 mt-1">
                            <div class="custom-control custom-switch mb-2" >
                                <input type="checkbox" name="nsfw" :checked="favouritesOnlyIsSelected" class="custom-control-input" id="favouritesOnly" @click="toggleFavouritesOnly">
                                <label class="custom-control-label" for="favouritesOnly" >
                                    Favourites Only
                                </label>
                            </div>
                        </div>
                    </div>

                    <div v-if="loadingInProgress" class="mt-4 d-flex justify-content-center">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    <div class="card mb-3" v-else>
                        <div class="container">
                            <div class="row" v-if="allMonsters.length > 0">
                                <div v-for="(monster, index) in allMonsters" class="monster col-lg-3 col-6" :key="monster.id">
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
                            <div v-else class="row">
                                <h3 class="pl-2"><i>No monsters here!</i></h3>
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
            user: {
                default: null,
                format: Object
            },
            groupId: Number
        },
        components: {
            monsterThumbnailComponent
        },
        methods: {
            sortByChanged: function() {
                this.filterChanged();
            },
            timeFilterChanged: function() {
                this.filterChanged();
            },
            searchClick: function() {
                this.filterChanged();
            },
            searchKeyUp: function() { 
                this.filterChanged();
            },
            toggleFavouritesOnly: function(){
                this.favouritesOnlyIsSelected = !this.favouritesOnlyIsSelected;
                this.filterChanged();
            },
            resetMonsters: function(){
                this.allMonsters = [];
            },
            filterChanged: function(){
                this.resetMonsters();
                this.loadMonsters();
            },
            loadMonsters: function(){
                var _this = this;
                _this.loadingInProgress=true;
                axios.post('/galleryNew/getData',{
                    action: 'getGalleryMonsters',
                    search: _this.enteredSearchText, 
                    timeFilter: _this.selectedTimeFilter,
                    sortBy: _this.selectedSortBy,
                    favouritesOnly: _this.favouritesOnlyIsSelected
                })
                .then((response) => {
                    _this.allMonsters.push.apply(_this.allMonsters,response.data);
                    _this.loadingInProgress=false;
                    console.log(response); 
                })
                .catch((error) => {
                    console.log(error);
                });
            },
        },
        computed: {
           
        },
        data() {
            return {
                selectedTimeFilter : 'week',
                enteredSearchText : '',
                selectedSortBy: 'highest_rated',
                allMonsters: [],
                loadingInProgress: false,
                favouritesOnlyIsSelected: false
            }
        },
        mounted() {
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
    .spinner-border{
        width:3.5rem;
        height:3.5rem;
    }
    .has-search .form-control {
        padding-left: 2.375rem;
    }
    .fa-search {
        color: #aaa;
    }
    .has-search .form-control-feedback {
        position: absolute;
        z-index: 2;
        display: block;
        width: 2.375rem;
        height: 2.375rem;
        line-height: 2.375rem;
        text-align: center;
        pointer-events: none;
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }

    @media (min-width: 768px) 
    {
         h3{
            font-size: 18px;
        }
    }
</style>
