<template>
    <div class="container">
        <div class="row justify-content-center">
            <div id="main-container" class="col-md-12">

                <div class="container">
                    <div class="row mb-2">
                        <div class="col-md-12">
                            Monsters Awaiting Bodies
                        </div>                      
                    </div>
                    <div class="row">
                        <div v-for="monster in monstersAwaitingBodies" :key="monster.id">
                            <monster-component
                                :monster="monster">
                            </monster-component>
                        </div>
                    </div>
                     <div class="row mb-2">
                        <div class="col-md-12">
                            Monsters Awaiting Legs
                        </div>                      
                    </div>
                    <div class="row">
                       <div v-for="monster in monstersAwaitingLegs" :key="monster.id">
                            <monster-component
                                :monster="monster">
                            </monster-component>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
</template>

<script>
    import monsterComponent from './Monster' ;
    export default {
        props: {
            segment_name: String, 
            monsters: Array
        },
        components: {
            monsterComponent
        },
        methods: {
          
        },
        computed: {
            monstersAwaitingBodies: function (){
                return this.monsters.filter(i => (i.status === 'awaiting body'))
            },
            monstersAwaitingLegs: function (){
                return this.monsters.filter(i => (i.status === 'awaiting legs'))
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

</style>
