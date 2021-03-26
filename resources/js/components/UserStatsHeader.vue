<template>
    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-lg-4 col-12 pl-0">
                <trophies-header 
                    class="d-inline-block"
                    :trophies="trophies">
                </trophies-header>
            </div>
            <div class="col-sm-6 col-lg-2 col-12">
                <label class="statLabel">Monsters:</label> {{ stats.monster_count }}
            </div>
            <div class="col-sm-6 col-lg-2 col-12">
                <label class="statLabel">Ratings:</label> {{ stats.rating_count }}
            </div>
            <div class="col-sm-6 col-lg-2 col-12">
                <label class="statLabel">Streak</label> 
                <i data-toggle="tooltip" data-placement="right" title="" class="pl-1 fa fa-info-circle" data-original-title="i.e. The most consecutive days monsters have been created on."></i>
                <span class="statLabel pl-1">:</span> {{ stats.top_streak }}
            </div>
            <div class="col-sm-6 col-lg-2 col-12">
                <label class="statLabel">Favourites:</label>
                <!-- <a v-if="stats.favourites_count > 0" :href="'/favourites/' + user.id" class="pl-1"> 
                    {{ stats.favourites_count }}
                </a> -->
                <span class="pl-1">
                    {{ stats.favourites_count }}
                </span>
            </div>
        </div>
    </div>
</template>

<script>
    import trophiesHeader from './TrophiesHeader';
    export default {
        props: {
          trophies: Array,
          stats: Object,
          user: Object
        },
        components: {
            trophiesHeader
        },
        methods: {
          
        },
        computed: {
            
        },
        data(){
            return {

            };
        },
        mounted() {
            console.log('Component mounted.');
            $(function () {
                $('[data-toggle="tooltip"]').tooltip()
            })
        },
    }
</script>
<style scoped>
   .statLabel{
       font-size:18px;
       font-weight:bold;
       margin-bottom:0px;
   }
</style>
