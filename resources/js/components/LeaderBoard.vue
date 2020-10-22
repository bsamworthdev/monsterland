<template>
  <div>
    <div class="ratings_week">
      <h5>Most Ratings this week</h5>
      <table class="table">
          <tr v-for="(stat, index) in stats.ratings_week" :key="index">
              <th scope="row">{{ (index+1) }}</th>
              <td><a :href="'/monsters/' + stat.user.id">{{ stat.user.name }}</a></td>
              <td>{{ stat.ratings_count }}</td>
          </tr>
      </table>
    </div>
    <div class="monsters_week">
      <h5>Most Monsters this week</h5>
      <table class="table">
          <tr v-for="(stat, index) in stats.monsters_week" :key="index">
              <th scope="row">{{ (index+1) }}</th>
              <td><a :href="'/monsters/' + stat.creator.id">{{ stat.creator.name }}</a></td>
              <td>{{ stat.monster_count }}</td>
          </tr>
      </table>
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

</style>
