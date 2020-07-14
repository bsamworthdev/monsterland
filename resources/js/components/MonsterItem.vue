<template>
    <div class="m-1">
        <button class="btn btn-info" 
            :disabled="createdByUser||inProgress"
            :class="{'createdByUser':createdByUser,'inProgress':inProgress}" 
            :title="getMonsterTitle()" 
            @click="loadMonster()">
            {{ monster.name }}
        </button>                      
    </div>
</template>

<script>
    export default {
        props: {
            monster: Object,
            createdByUser: Boolean,
            inProgress: Boolean
        },
        methods: {
            loadMonster: function(){
                if (!this.createdByUser && !this.inProgress){
                    location.href = '/canvas/' + this.monster.id;
                }
            },
            getMonsterTitle: function(){
                if (this.createdByUser){
                    return 'You cannot add to your own monster';
                } else if(this.inProgress){
                    return 'In Progress...';
                } else {
                    return 'Click to draw';
                }
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
