<template>
  <div>
    <a :href="'/gallery/' + monster.id">
        <img class="monsterland_featured noshare myShadow rounded mb-2" :src="'/storage/' + monster.id + '.png'" :alt="monster.name">
    </a>
    <h5>
        <a class="text-dark" :href="'/gallery/' + monster.id">
            <b>{{ monster.name }}</b>
        </a>
    </h5>
    <p>
        <i>Created: {{ monster.created_at_tidy }}</i>
    </p>
 
  </div>
</template>

<script>
    export default {
        props: {
          monster: Object
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
  .monsterland_featured{
      width:100%;
      margin:-3px;
      object-fit:cover;
      border:3px solid black;
  }

</style>
