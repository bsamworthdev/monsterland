<template>
  <div>
    <div class="card">
        <div id="carouselExampleIndicators" class="carousel" data-interval="false" data-ride="carousel">
        <ol class="carousel-indicators mb-0 slide d-none">
            <li v-for="(monster, index) in monsters" :key="index" data-target="#carouselExampleIndicators" data-slide-to="index" :class="{'active':index==-1}"></li>
        </ol>
        <div class="carousel-inner">
             <div class="carousel-item mb-3 active">
                <div class="monsterPage text-center w-100">
                    <h3>{{ bookTitle }}</h3>
                    <img src="/storage/757.png">
                </div>
            </div>
            <div class="carousel-item mb-3" :class="{'active':index==-1}"  v-for="(monster, index) in monsters" :key="index">
                <div class="monsterPage text-center w-100">
                    <h3>{{ monster.name }}</h3>
                    <img :src="monster.image">
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
 
  </div>
</template>

<script>
    export default {
        props: {
          monsters: Array,
          bookTitle: String
        },
        methods: {
            
        },
        computed: {
           
        },
        data() {
            return {
                
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
    background-image: url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%23C0C0C0' viewBox='0 0 8 8'%3E%3Cpath d='M5.25 0l-4 4 4 4 1.5-1.5-2.5-2.5 2.5-2.5-1.5-1.5z'/%3E%3C/svg%3E") !important;
  }
  .carousel-control-next-icon {
    background-image: url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%23C0C0C0' viewBox='0 0 8 8'%3E%3Cpath d='M2.75 0l-1.5 1.5 2.5 2.5-2.5 2.5 1.5 1.5 4-4-4-4z'/%3E%3C/svg%3E") !important;
  }
  .carousel-control-prev:hover .carousel-control-prev-icon, 
  .carousel-control-next:hover .carousel-control-next-icon{
    display:block;
  }
  .carousel-control-prev{
    left:-10px;
  }
  .carousel-control-next{
    right:-10px;
  }

</style>
