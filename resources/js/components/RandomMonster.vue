<template>
    <div id="app">
        <div class="container">
            <div class="row">
                <a :href="'/gallery/' + fetchedMonster.id">
                    <img class="monsterland_featured noshare myShadow rounded mb-2" :src="'/storage/' + fetchedMonster.id + '.png'" :alt="fetchedMonster.name">
                </a>
            </div>
            <div class="row">
                <h5>
                    <a class="text-dark" :href="'/gallery/' + fetchedMonster.id">
                        <b>{{ fetchedMonster.name }}</b>
                    </a>
                </h5>
            </div>
            <div class="row">
                <p>
                    <i>Created: {{ fetchedMonster.created_at_tidy }}</i>
                </p>
            </div>
            <div class="row">
                <button class="btn btn-success btn-sm" @click="refresh">
                    <i class="fa fa-redo"></i> Get Another!
                </button>
            </div>
            
        </div>
    </div>
</template>

<script>
    export default {
        props: {
          monster: Object
        },
        methods: {
            refresh: function(){
                axios.post('/fetchRandomMonster')
                .then((response) => {
                    this.fetchedMonster = response.data;
                })
                .catch((error) => {
                    console.log(error);
                });
            }
        },
        computed: {
           
        },
        data() {
            return {
                fetchedMonster: this.monster
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
