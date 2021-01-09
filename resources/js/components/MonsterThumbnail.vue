<template>
    <div class="m-1">
        <div class="card"
            @click="loadMonster()">
            <div class="card-header">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="monster_name">{{ monster.name }}</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-7 col-12">
                            <div class="monster_rating">Rating: {{ averageRating }}</div>
                        </div>
                        <div v-if="user">
                            <div v-if="isMyPage" class="col-xl-5 col-12" >
                                <div v-if="mySegment()" class="monster_rating">
                                    <p class="mySegment text-info" :title="'You drew the ' + mySegment()"> 
                                        <i class="fas fa-smile"></i>
                                        {{ mySegment() }}
                                    </p>
                                </div>
                                <div v-else-if="myRating()" class="monster_rating">
                                    <p class="rated text-success" :title="'You rated this ' + myRating()">
                                        <i class="fa fa-check"></i>
                                        Rated
                                    </p>
                                </div>
                                <div v-else class="monster_rating">
                                    <p class="unrated text-danger" title="You have not rated this">
                                        <i class="fa fa-times"></i>
                                        Unrated
                                    </p>
                                </div>
                            </div>
                            <div v-else>
                                <div v-if="mySegment()" class="monster_rating">
                                    <p class="mySegment text-info" :title="user.name + ' drew the ' + mySegment()"> 
                                        <i class="fas fa-smile"></i>
                                        {{ mySegment() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div v-if="monster.image && monster.image!='n/a'" class="container monster_container useImage">
                    <img :src="monster.image">
                </div>
                <div v-else class="container monster_container">
                    <div class="row headSegment">
                        <img :src="getSegmentImage('head')">
                    </div>
                    <div class="row bodySegment">
                        <img :src="getSegmentImage('body')">
                    </div>
                    <div class="row legsSegment">
                        <img :src="getSegmentImage('legs')">
                    </div>
                </div>
            </div>
        </div>                      
    </div>
</template>

<script>
    export default {
        props: {
            user: {
                default:null,
                type: Object,
            },
            monster: Object,
            monsterSequenceNum: Number,
            timeFilter: String,
            search: {
                default:null,
                type: String
            },
            pageType: {
                format: String,
                default:'gallery'     
            },
            isMyPage:{
                default: true,
                format: Boolean
            },
        },
        methods: {
            loadMonster: function(){
                if (this.pageType == 'gallery'){
                    location.href = '/gallery/' + this.monster.id;
                } else {
                    location.href = '/halloffamesingle/' + this.monsterSequenceNum + '/' + this.timeFilter + '/' + this.search ;
                }
            },
            getSegmentImage: function(segment) {
                for (var i = 0; i < this.monster.segments.length; i ++){
                    if (this.monster.segments[i].segment == segment){
                        return this.monster.segments[i].image;
                    }
                }
                return '';
            },
            mySegment: function() {
                if (this.user){
                    for (var i = 0; i < this.monster.segments.length; i ++){
                        if (this.monster.segments[i].created_by == this.user.id){
                            return this.monster.segments[i].segment;
                        }
                    }
                }
                return '';
            },
            myRating: function() {
                if (this.user){
                    var ratings = this.monster.ratings;
                    for (var i = 0; i < ratings.length; i++){
                        if (ratings[i].user_id == this.user.id){
                            return ratings[i].rating;
                        }
                    }
                }
                return 0;
            },
        },
        computed: {
            averageRating: function(){
                return Number((this.monster.average_rating)).toFixed(2);
            }
        },
        data() {
            return {
            }
        },
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>

<style scoped>
    .inProgress{
        background-color:rgba(192, 192, 192, 0.589);
    }
    .createdByUser{
        background-color:#FFF;
        opacity:1!important;
        border:none;
    }
    .headSegment, .bodySegment, .legsSegment {
        margin-left: 0px;
        margin-right: 0px;
    }
    .bodySegment, .legsSegment {
        margin-top: -9px;
    }
    .monster_name{
        font-size:14px;
        font-weight:bold;
    }
    .monster_rating{
        clear:both;
        cursor:default;
        white-space:nowrap;
    }
    .monster_rating > p{
        margin-bottom:0px;
    }
    .card-header{
        padding:0.5rem 0.5rem!important;
    }
    .monster_container {
        padding:0.25rem!important;
    }
    .monster_container.useImage img{
        max-width: 100%;
        max-height: 100%;
    }
    .mySegment{
        cursor:default;
    }
    .card-body{
        padding:0.25rem!important;
    }

    @media only screen and (max-width: 340px) {
        .monster_container:not(.useImage) {
            transform:scaleX(0.06) scaleY(0.06);
            transform-origin:top left;
            height: 40px;
        }
        .monster_name{
            font-size:9px
        }
        .monster_rating{
            font-size:8px
        }
    }

    @media only screen and (min-width: 341px) {
        .monster_container:not(.useImage){
            transform:scaleX(0.09) scaleY(0.09);
            transform-origin:top left;
            height: 70px;
        }
        .monster_name{
            font-size:11px
        }
        .monster_rating{
            font-size:10px
        }
    }

    @media only screen and (min-width: 400px) {
        .monster_container:not(.useImage){
            transform:scaleX(0.12) scaleY(0.12);
            transform-origin:top left;
            height: 90px;
        }
        .monster_name{
            font-size:14px
        }
        .monster_rating{
            font-size:12px
        }
    }

    @media only screen and (min-width: 500px) {
        .monster_container:not(.useImage){
            transform:scaleX(0.18) scaleY(0.18);
            transform-origin:top left;
            height: 140px;
        }
        .monster_name{
            font-size:14px
        }
        .monster_rating{
            font-size:12px
        }
    }

    @media only screen and (min-width: 600px) {
        .monster_container:not(.useImage){
            transform:scaleX(0.20) scaleY(0.20);
            transform-origin:top left;
            height: 150px;
        }
        .monster_name{
            font-size:14px
        }
        .monster_rating{
            font-size:12px
        }
    }

    @media only screen and (min-width: 800px) {
        .monster_container:not(.useImage){
            transform:scaleX(0.24) scaleY(0.24);
            transform-origin:top left;
            height: 180px;
        }
        .monster_name{
            font-size:13px
        }
        .monster_rating{
            font-size:11px
        }
    }

    @media only screen and (min-width: 992px) {
        .monster_container:not(.useImage){
            transform:scaleX(0.13) scaleY(0.13);
            transform-origin:top left;
            height: 100px;
        }
    }

    @media only screen and (min-width: 1025px) {
        .monster_container:not(.useImage){
            transform:scaleX(0.15) scaleY(0.15);
            transform-origin:top left;
            height: 120px;
        }
    }

    @media only screen and (min-width: 1201px) {
        .monster_container:not(.useImage){
            transform:scaleX(0.18) scaleY(0.18);
            transform-origin:top left;
            height: 140px;
        }
    }
</style>
