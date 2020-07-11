<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">

                <div class="card">
                    <div class="card-header">
                        <div class="container monster-header">
                            <div class="row">
                                <div class="col-3">
                                    <button class="btn btn-info" :disabled="lockPrev" @click="prevClick">Previous</button>
                                </div>
                                <div class="col-6">
                                    <h2>{{ monster.name }}</h2>
                                </div>
                                <div class="col-3">
                                    <button class="btn btn-info" :disabled="lockNext" @click="nextClick">Next</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="container">
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
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            monster: Object,
            prevMonster: Object,
            nextMonster: Object
        },
        methods: {
           getSegmentImage: function(segment) {
                for (var i = 0; i < this.monster.segments.length; i ++){
                    if (this.monster.segments[i].segment == segment){
                        return this.monster.segments[i].image;
                    }
                }
                return '';
            },
            prevClick: function() {
                location.href = '/gallery/' + this.prevMonster.id;
            },
            nextClick: function() {
                location.href = '/gallery/' + this.nextMonster.id;
            }
        },
        computed: {
            lockPrev: function(){
                return this.prevMonster.id==this.monster.id;
            },
            lockNext: function(){
                return this.nextMonster.id==this.monster.id;
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
    .monster-header{
        text-align:center;
    }
    .bodySegment, .legsSegment {
        margin-top: -33px;
    }
</style>
