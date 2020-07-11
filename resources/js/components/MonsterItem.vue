<template>
    <div class="m-1">
        <button class="btn btn-info" :disabled="locked" :title="getMonsterTitle" @click="loadMonster()">
            {{ monster.name }}
        </button>                      
    </div>
</template>

<script>
    export default {
        props: {
            monster: Object,
            locked: Boolean
        },
        methods: {
            loadMonster: function(){
                location.href = '/canvas/' + this.monster.id;
            },
            getMonsterTitle: function(){
                if (this.locked){
                    return 'You cannot add to your own monster';
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

</style>
