<template>
    <div class="container">
        <div class="row justify-content-center">
            <div id="main-container" class="col-md-12">

                <div class="container">
                    <div class="row">
                        <div class="col-lg-3 col-6 mt-1">
                            <select id="sortBy" v-model="selectedSortBy" class="form-control" @change.stop="sortByChanged($event)">
                                <option value="highest_rated">Highest Rated</option>
                                <option value="lowest_rated">Lowest Rated</option>
                                <option value="newest">Newest</option>
                                <option value="oldest">Oldest </option>
                            </select>
                        </div>
                        <div class="col-lg-3 col-6 mt-1">
                            <select id="timeFilter" v-model="selectedTimeFilter" class="form-control" @change.stop="timeFilterChanged($event)">
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
                                <input id="searchText" placeholder="Search" class="form-control" type="text" v-model="enteredSearchText" @keyup.stop="searchKeyUp" />
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3" v-if="user && pageType !== 'usermonsters'">
                        <div class="col-lg-3 col-6">
                            <div class="custom-control custom-switch mb-2" >
                                <input type="checkbox" name="favouritesOnly" :checked="favouritesOnlyIsSelected" class="custom-control-input" id="favouritesOnly" @click.stop="toggleFavouritesOnly">
                                <label class="custom-control-label" for="favouritesOnly" >
                                    Favourites Only
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="custom-control custom-switch mb-2" >
                                <input type="checkbox" name="followedOnly" :checked="followedOnlyIsSelected" class="custom-control-input" id="followedOnly" @click.stop="toggleFollowedOnly">
                                <label class="custom-control-label" for="followedOnly" >
                                    Followed Artists Only
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6" v-show="userJSON.allow_nsfw">
                            <div class="custom-control custom-switch mb-2" >
                                <input type="checkbox" name="nsfwOnly" :checked="nsfwOnlyIsSelected" class="custom-control-input" id="nsfwOnly" @click.stop="toggleNsfwOnly">
                                <label class="custom-control-label" for="nsfwOnly" >
                                    <span style="color:red">NSFW</span> Only
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="custom-control custom-switch mb-2" >
                                <input type="checkbox" name="unratedOnly" :checked="unratedOnlyIsSelected" class="custom-control-input" id="unratedOnly" @click.stop="toggleUnratedOnly">
                                <label class="custom-control-label" for="unratedOnly" >
                                    Unrated Only
                                </label>
                            </div>
                        </div>
                    </div>

                    <div v-if="loadingInProgress" id="spinnerMain" class="mt-4 mb-3 d-flex justify-content-center">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                    <div class="card mb-3 mt-2" v-else>
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
                            <div v-else-if="reachedEnd" class="row">
                                <h3 class="pl-3 pt-2 pb-2"><i>No monsters here!</i></h3>
                            </div>
                            <div v-if="loadingMoreInProgress" id="spinnerMore" class="mt-4 mb-3 d-flex justify-content-center">
                                <div class="spinner-border" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                            <div v-else id="lazyLoadTrigger">
                                <!-- If you can see this then load more... -->
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
            groupId: Number,
            pageType: String
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
            toggleFollowedOnly: function(){
                this.followedOnlyIsSelected = !this.followedOnlyIsSelected;
                this.filterChanged();
            },
            toggleNsfwOnly: function(){
                this.nsfwOnlyIsSelected = !this.nsfwOnlyIsSelected;
                this.filterChanged();
            },
            toggleUnratedOnly: function(){
                this.unratedOnlyIsSelected = !this.unratedOnlyIsSelected;
                this.filterChanged();
            },
            resetMonsters: function(){
                this.allMonsters = [];
            },
            filterChanged: function(){
                this.resetMonsters();
                this.loadingInProgress=true;
                this.loadingMoreInProgress=false;
                this.prepareToLoadMonsters();
            },
            prepareToLoadMonsters: function() {
                if (this.cancel) {
                    this.cancel();
                } 
                this.loadMonsters();
            },
            loadMonsters: function(){
                var _this = this;
                axios.post('/galleryNew/getData',{
                    action: 'getGalleryMonsters',
                    search: _this.enteredSearchText, 
                    timeFilter: _this.selectedTimeFilter,
                    sortBy: _this.selectedSortBy,
                    favouritesOnly: _this.favouritesOnlyIsSelected,
                    userFavouritesOnly: _this.userFavouritesOnly,
                    followedOnly: _this.followedOnlyIsSelected,
                    nsfwOnly: _this.nsfwOnlyIsSelected,
                    unratedOnly: _this.unratedOnlyIsSelected,
                    myMonstersOnly: _this.myMonstersOnly,
                    userMonstersOnly: _this.userMonstersOnly,
                    skip: _this.allMonsters.length
                },{
                    cancelToken: new _this.CancelToken(function executor(c) {
                        _this.cancel = c;
                    })
                })
                .then((response) => {
                    var monsters = response.data;
                    if (monsters.length){
                        _this.allMonsters.push.apply(_this.allMonsters,monsters);
                    }
                    if (monsters.length == 8){
                        _this.reachedEnd = false;
                    } else {
                        _this.reachedEnd = true;
                    }
                    _this.loadingInProgress=false;
                    _this.loadingMoreInProgress = false;
                    
                    console.log(response); 
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            checkVisible: function(elm_id) {
                var elm = document.getElementById(elm_id);
                if (!elm) return false;
                var rect = elm.getBoundingClientRect();
                var viewHeight = Math.max(document.documentElement.clientHeight, window.innerHeight);
                return !(rect.bottom < 0 || rect.top - viewHeight >= 0);
            },
            setDefaults: function(){
                switch (this.pageType){
                    case 'gallery':
                    break;
                    case 'halloffame':
                        this.selectedTimeFilter = 'ever';
                        this.selectedSortBy = 'highest_rated';
                        break;
                    case 'favourites':
                        this.favouritesOnlyIsSelected = true;
                        break;
                    case 'userfavourites':
                        this.userFavouritesOnly = 2;
                        break;
                    case 'mymonsters':
                        this.myMonstersOnly = true;
                        this.selectedTimeFilter = 'ever';
                    case 'usermonsters':
                        this.userMonstersOnly = this.userJSON.id;
                        this.selectedTimeFilter = 'ever';
                    break;
                }
            }
        },
        computed: {
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
                selectedTimeFilter : 'week',
                enteredSearchText : '',
                selectedSortBy: 'newest',
                allMonsters: [],
                loadingInProgress: false,
                loadingMoreInProgress: false,
                favouritesOnlyIsSelected: false,
                followedOnlyIsSelected: false,
                nsfwOnlyIsSelected: false,
                unratedOnlyIsSelected: false,
                userMonstersOnly: 0,
                userFavouritesOnly: false,
                myMonstersOnly: false,
                reachedEnd: false,
                cancel: false,
                CancelToken: axios.CancelToken,
            }
        },
        mounted() {
            console.log('Component mounted.')
            var _this = this;
            _this.setDefaults();
            setInterval(function(){
                if (_this.loadingMoreInProgress) return;

                if (!_this.reachedEnd && _this.checkVisible('lazyLoadTrigger')){
                    _this.loadingMoreInProgress = true;
                    _this.loadMonsters();
                } else {
                     _this.loadingMoreInProgress = false;
                }
            },100)
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
    #spinnerMain .spinner-border{
        width:3.5rem;
        height:3.5rem;
    }
    #spinnerMore .spinner-border{
        width:2.5rem;
        height:2.5rem;
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
