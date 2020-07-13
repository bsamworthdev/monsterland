<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">

                <div class="card">
                    <div class="card-header">
                        <div class="container monster-header">
                            <div class="row">
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
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h1>{{ monster.name }}</h1>
                                </div>
                            </div>
                            <div class="row mt-1">
                                <div class="col-4">
                                    <h5>Head: <b>{{ monster.segments[0].creator.name }}</b></h5>
                                </div>
                                <div class="col-4 ">
                                    <h5>Body: <b>{{ monster.segments[1].creator.name }}</b></h5>
                                </div>
                                <div class="col-4">
                                    <h5>Legs: <b>{{ monster.segments[2].creator.name }}</b></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="canvas_container" class="card-body">
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

    @media only screen and (max-width: 1024px) {
        #canvas_container{
            transform:scaleX(0.78) scaleY(0.78);
            transform-origin:top left;
            height: 780px;
        }
    }

    @media only screen and (max-width: 900px) {
        #canvas_container{
            transform:scaleX(0.55) scaleY(0.55);
            transform-origin:top left;
            height: 500px;
        }
    }

    @media only screen and (max-width: 800px) {
        #canvas_container{
            transform:scaleX(0.48) scaleY(0.48);
            transform-origin:top left;
            height: 480px;
        }
    }

    @media only screen and (max-width: 600px) {
        #canvas_container{
            transform:scaleX(0.44) scaleY(0.44);
            transform-origin:top left;
            height: 440px;
        }
     }


    @media only screen and (max-width: 500px) {
        #canvas_container{
            transform:scaleX(0.4) scaleY(0.4);
            transform-origin:top left;
            height: 400px;
        }
     }

     @media only screen and (max-width: 450px) {
        #canvas_container{
            transform:scaleX(0.33) scaleY(0.33);
            transform-origin:top left;
            height: 330px;
        }
        .btnLabel{
            display:none;
        }
     }

     @media only screen and (max-width: 400px) {
        #canvas_container{
            transform:scaleX(0.28) scaleY(0.28);
            transform-origin:top left;
            height: 280px;
        }
        .btnLabel{
            display:none;
        }
     }

    @media only screen and (max-width: 350px) {
        #canvas_container{
            transform:scaleX(0.23) scaleY(0.23);
            transform-origin:top left;
            height: 230px;
        }
        .btnLabel{
            display:none;
        }
     }

</style>
