<template>
    <div class="suggested_tags">
      <table class="table">
          <tr v-for="(tag, index) in tags" :key="index">
              <td>
                <a class="d-inline-block text-truncate" style="max-width: 7rem" :href="'/monstergrid/gallery/0/tag:' + tag.name">
                  <div class="alert alert-info monsterTag pt-0 pb-0">#{{ tag.name }}</div>
                </a>
              </td>
              <td>
                  ({{ tag.tag_count }} Monsters)
              </td>
          </tr>
      </table>
    </div>
</template>

<script>
    export default {
        props: {
          tags: Object
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
  .table th, .table td {
    border:none!important;
    padding-bottom:0.25rem;
  }
  .monsterTag{
      cursor:pointer;
      margin-bottom:0px;
      /* white-space:break-spaces; */
      position:relative;
  }
  a{
    max-width:15rem;
  }
</style>
