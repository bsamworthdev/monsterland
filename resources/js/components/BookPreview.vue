<template>
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-6">
                    <h4 class="pull-left">Preview Book</h4>
                </div>
                <div class="col-3">
                    <button class="btn btn-info pull-right btn-block" 
                        @click="backClick()">
                        Back
                    </button>
                </div>
                <div class="col-3">
                    <button id="placeOrder" class="btn btn-success pull-right btn-block" @click="activeModal=1;">Place An Order</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div>
                <div class="card">
                    <div id="carouselExampleIndicators" class="carousel" data-interval="false" data-ride="carousel">
                    <ol class="carousel-indicators mb-0 slide d-none">
                        <li v-for="(monster, index) in monsters" :key="index" data-target="#carouselExampleIndicators" data-slide-to="index" :class="{'active':index==-1}"></li>
                    </ol>
                    <div class="carousel-inner">
                        <div class="carousel-item mb-3 active">
                            <div class="monsterPage text-center w-100">
                                <img src="/storage/757.png" class="monsterImage noshare">
                                <h1 v-if="editMode" id="editableBookTitle">
                                    <input id="editBookTitle" type="text" v-model="enteredBookTitle">
                                    <button class="btn btn-success" @click="saveTitle();" title="Save">
                                        <i class="fa fa-check"></i>
                                    </button>
                                    <button class="btn btn-danger" @click="cancelTitle();"  title="Cancel">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </h1>
                                <h1 v-else id="bookTitle" class="pl-5" @click="editTitle()">
                                    {{ enteredBookTitle }} 
                                    <i id="editTitleIcon" class="fa fa-pen" title="Edit Title"></i>
                                </h1> 
                            </div>
                        </div>
                        <div class="carousel-item mb-3" :class="{'active':index==-1}"  v-for="(monster, index) in monsters" :key="index">
                            <div class="monsterPage text-center w-100">
                                <h1>{{ monster.name }}</h1>                    
                                <div class="row">
                                    <div class="col-4">
                                        <h5>Head: 
                                            <b v-if="getCreator(monster, 'head').id > 0"> {{ getCreator(monster, 'head').name }}</b>
                                            <b v-else-if="getCreatorGroupUserName(monster, 'head')">{{ getCreatorGroupUserName(monster, 'head') }}</b>
                                            <b v-else>GUEST</b>
                                        </h5>
                                    </div>
                                    <div class="col-4 ">
                                        <h5>Body: 
                                            <b v-if="getCreator(monster, 'body').id > 0"> {{ getCreator(monster, 'body').name }}</b>
                                            <b v-else-if="getCreatorGroupUserName(monster, 'body')">{{ getCreatorGroupUserName(monster, 'body') }}</b>
                                            <b v-else>GUEST</b>
                                        </h5>
                                    </div>
                                    <div class="col-4">
                                        <h5>Legs: 
                                            <b v-if="getCreator(monster, 'legs').id > 0"> {{ getCreator(monster, 'legs').name }}</b>
                                            <b v-else-if="getCreatorGroupUserName(monster, 'legs')">{{ getCreatorGroupUserName(monster, 'legs') }}</b>
                                            <b v-else>GUEST</b>
                                        </h5>
                                    </div>
                                </div>
                                <img :src="monster.image" class="monsterImage noshare">
                                <div class="row">
                                    <div class="col-12">Page {{ index+1 }}</div>
                                </div>
                            </div>
                        </div>
                    
                    </div>
                    <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                    </div>
                </div>
            
                <book-order-component
                        v-if="activeModal==1" 
                        @close="activeModal=0"
                        :quantity="quantity"
                        :address="address">
                </book-order-component>
                <div v-if="activeModal > 0" class="modal-backdrop fade show"></div>
            </div>
        </div>
     </div>
</template>

<script>
    import bookOrderComponent from './BookOrder' ;
    export default {
        props: {
          monsters: Array,
          bookTitle: String,
          book: Object,
          quantity: Number,
          address: Object
        },
        components: {
            bookOrderComponent
        },
        methods: {
            editTitle: function(){
                this.prevEnteredBookTitle = this.enteredBookTitle;
                this.editMode=true;
            },
            saveTitle: function(){
                axios.post('/book/update',{
                    bookId: this.book.id,
                    field: 'title',
                    value: this.enteredBookTitle           
                })
                .then((response) => {
                    this.editMode=false;
                    console.log(response); 
                })
                .catch((error) => {
                    console.log(error);
                });
                
            },
            cancelTitle: function(){
                this.enteredBookTitle = this.prevEnteredBookTitle
                this.editMode=false;
            },
            getCreator: function(monster, segment_name){
                var segments = monster.segments;
                for (var i = 0; i < segments.length; i ++){
                    if (segments[i].segment == segment_name){
                        if (segments[i].creator){
                            return segments[i].creator;
                        }
                    }
                }
                return {
                    'id':0,
                    'name':'GUEST'
                };
            },
            getCreatorGroupUserName: function(monster, segment_name){
                var segments = monster.segments;
                for (var i = 0; i < segments.length; i ++){
                    if (segments[i].segment == segment_name){
                        if (segments[i].created_by_group_username){
                            return segments[i].created_by_group_username;
                        }
                    }
                }
                return false;
            },
            backClick: function(){
                window.location.href='/book/build/' + this.book.group_id + '/' + this.book.id;
            }
        },
        computed: {
           
        },
        data() {
            return {
                editMode:false,
                enteredBookTitle:this.bookTitle,
                prevEnteredBookTitle:this.bookTitle,
                activeModal: 0,
            }
        },
        mounted() {
            console.log('Component mounted.')
            
           
        },
    }
</script>

<style scoped>
    .carousel-indicators li.active{
        background-color: darkgray;
    }
    .carousel-indicators li{
        background-color: #C0C0C0;
    }
    .carousel-control-prev-icon {
        background-image: url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%23000000' viewBox='0 0 8 8'%3E%3Cpath d='M5.25 0l-4 4 4 4 1.5-1.5-2.5-2.5 2.5-2.5-1.5-1.5z'/%3E%3C/svg%3E") !important;
    }
    .carousel-control-next-icon {
        background-image: url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%23000000' viewBox='0 0 8 8'%3E%3Cpath d='M2.75 0l-1.5 1.5 2.5 2.5-2.5 2.5 1.5 1.5 4-4-4-4z'/%3E%3C/svg%3E") !important;
    }
    .carousel-control-prev-icon,
    .carousel-control-next-icon{
        width:50px;
        height:50px;
    }

  .carousel-control-prev{
      justify-content:left;
      padding-left:10px;

  }
  .carousel-control-next{
      justify-content:flex-end;
      padding-right:10px;
  }
  .monsterImage{
      width:calc(100% - 150px);
  }
  #bookTitle:hover #editTitleIcon{
      /* display:inline-block!important; */
      visibility:visible;
  }
  #editTitleIcon{
      visibility:hidden;
      cursor:pointer;
  }

</style>
