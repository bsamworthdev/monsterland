<template>
    <div class="m-1">
        <div class="card"
            @click="loadMonster()">
            <h4>{{ monster.name }}</h4>
            <p>{{ averageRating }}</p>
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
            },
        },
        computed: {
            averageRating: function(){
                return Number((this.monster.average_rating)).toFixed(2);
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
    .inProgress{
        background-color:rgba(192, 192, 192, 0.589);
    }
    .createdByUser{
        background-color:#FFF;
        opacity:1!important;
        border:none;
    }
</style>
