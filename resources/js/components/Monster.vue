<template>
    <div class="container">
        <div class="row justify-content-center">
            <div id="main-container" class="col-md-12">

                <div class="container">
                    <div class="row mb-2">
                        <div class="col-md-12" @click="loadMonster()">
                            {{ monster.name }}
                        </div>                      
                    </div>
                    <div class="row">
                      
                    </div>       
                </div>
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
            loadMonster: function(){
                location.href = '/canvas/' + this.monster.id;
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
