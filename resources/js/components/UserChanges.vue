<template>
  <div class="latest_changes">
    <table class="table w-100 ">
        <tr v-for="(changes, index) in changes" :key="index">
            <td>
              <small>{{ changes.created_at_tidy }}</small>
            </td>
            <td v-if="changes.type=='segment_completed' || changes.type=='comment'">
              <a v-if="changes.user" class="position:absolute" style="max-width: 7rem" :href="'/monsters/' + changes.user.id">
                {{ changes.user.name }}
              </a>
              <span v-else>GUEST</span>
              {{ changes.action }}
              <a class="position:absolute" style="max-width: 7rem" :href="'/canvas/' + changes.monster.id">
                {{ changes.monster.name }}
              </a>
            </td>
            <td v-else-if="changes.type=='monster_completed'">
              {{ changes.action }}
              <a class="position:absolute" style="max-width: 7rem" :href="'/gallery/' + changes.monster.id">
                {{ changes.monster.name }}
              </a>
            </td>
            <td v-else-if="changes.type=='rating'">
              <a class="position:absolute" style="max-width: 7rem" :href="'/gallery/' + changes.monster.id">
                {{ changes.monster.name }}
              </a>
              {{ changes.action }}
            </td>
        </tr>
    </table>
  </div> 
</template>

<script>
    export default {
        props: {
          changes: Array
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
.table tr:first-of-type td{
  border-top: 0!important;
}

</style>
