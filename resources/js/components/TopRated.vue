<template>
    <div class="container">
        <div class="row justify-content-center">
            <div id="main-container" class="col-md-12">

                <div class="container">
                    <div class="row mb-2">
                        <h4>Top monsters- last 7 days</h4>
                    </div>
                    <div class="card mb-3">
                        <div class="container">
                            <div class="row">
                                <div v-for="monster in monsters" class="monster col-lg-3 col-6" :key="monster.id">
                                    <monster-thumbnail-component
                                        :monster="monster">
                                    </monster-thumbnail-component>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
</template>

<script>
    import monsterThumbnailComponent from './MonsterThumbnail' ;
    export default {
        props: {
            monsters: Array
        },
        components: {
            monsterThumbnailComponent
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
        }
    }
</script>

<style scoped>
    .monster{
        padding:0!important;
    }
</style>
