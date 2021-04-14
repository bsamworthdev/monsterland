<template>
  <div>

    <div id="carouselExampleIndicators" class="carousel slide" data-interval="10000" data-ride="carousel">
      <ol class="carousel-indicators mb-0">
        <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
        <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
        <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
      </ol>
      <div class="carousel-inner">
        <div class="carousel-item mb-3 active">
          <div class="ratings_week">
            <h5>Most Monsters Rated</h5>
            <table class="table">
                <tr v-for="(stat, index) in stats.ratings_week" :key="index">
                    <th scope="row">{{ (index+1) }}</th>
                    <td>
                      <a class="d-inline-block text-truncate" style="max-width: 7rem" :href="'/monsters/' + stat.user.id">
                        {{ stat.user.name }} <i title="pro user" v-if="stat.user.vip" class="fa fa-star"></i>
                      </a>
                    </td>
                    <td>{{ stat.ratings_count }}</td>
                </tr>
            </table>
          </div>
        </div>
        <div class="carousel-item mb-3">
          <div class="monsters_week">
            <h5>Most Monsters Drawn</h5>
            <table class="table">
                <tr v-for="(stat, index) in stats.monsters_week" :key="index">
                    <th scope="row">{{ (index+1) }}</th>
                    <td>
                      <a class="d-inline-block text-truncate" style="max-width: 7rem" :href="'/monsters/' + stat.creator.id">
                        {{ stat.creator.name }} <i title="pro user" v-if="stat.creator.vip" class="fa fa-star"></i>
                      </a>
                    </td>
                    <td>{{ stat.monster_count }}</td>
                </tr>
            </table>
          </div>
        </div>
        <div class="carousel-item mb-3">
          <div class="monsters_week">
            <h5>Most Tags Added</h5>
            <table class="table">
                <tr v-for="(stat, index) in stats.tagging_week" :key="index">
                    <th scope="row">{{ (index+1) }}</th>
                    <td>
                      <a class="d-inline-block text-truncate" style="max-width: 7rem" :href="'/monsters/' + stat.user.id">
                        {{ stat.user.name }} <i title="pro user" v-if="stat.user.vip" class="fa fa-star"></i>
                      </a>
                    </td>
                    <td>{{ stat.tag_count }}</td>
                </tr>
            </table>
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
</template>

<script>
    export default {
        props: {
          stats: Object
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
    display:none;
  }
  .carousel-control-next-icon {
    background-image: url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%23C0C0C0' viewBox='0 0 8 8'%3E%3Cpath d='M2.75 0l-1.5 1.5 2.5 2.5-2.5 2.5 1.5 1.5 4-4-4-4z'/%3E%3C/svg%3E") !important;
    display:none;
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
