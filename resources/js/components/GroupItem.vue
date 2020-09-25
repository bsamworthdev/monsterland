<template>
    <div class="m-1">
        <div class="card">
            <div class="card-header">
                <div class="group_name">{{ group.name }}</div>
            </div>
            <div class="card-body stats">
                <div class="statTitle">
                    <b>Created:</b> {{ group.created_at_date }}
                </div>
                <div class="statTitle mt-2">
                    <b>Monsters:</b> {{ completeMonsterCount + incompleteMonsterCount }}
                </div>
                <p class="complete">- Complete: {{ completeMonsterCount }}</p>
                <p class="incomplete">- Incomplete: {{ incompleteMonsterCount }}</p>

                <div class="statTitle mt-2">
                    <b>Code:</b> {{ group.code }}
                    <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="right" title="Give this code to anyone you want to invite to your group."></i>
                </div>
            </div>
        </div>                      
    </div>
</template>

<script>
    export default {
        props: {
            group: Object,
        },
        methods: {

        },
        computed: {
            completeMonsterCount: function(){
                var count = 0;
                var monsters = this.group.monsters;
                for(var i = 0; i < monsters.length; i++){
                    if (monsters[i].status == 'complete'){
                        count ++;
                    }
                }
                return count;
            },
            incompleteMonsterCount: function(){
                var count = 0;
                var monsters = this.group.monsters;
                for(var i = 0; i < monsters.length; i++){
                    if (monsters[i].status == 'awaiting body' || monsters[i].status == 'awaiting legs' ){
                        count ++;
                    }
                }
                return count;
            }
        },
        data() {
            return {
            }
        },
        mounted() {
            $(function () {
                $('[data-toggle="tooltip"]').tooltip()
            })
            console.log('Component mounted.')
        }
    }
</script>

<style scoped>
    .group_name{
        font-size:18px;
    }
    .stats .statTitle{
        font-size:16px;
    }
    .stats p{
        margin-bottom:0px;
        margin-left:4px;
    }
    .stats p.complete{
        color:green;
    }
    .stats p.incomplete{
        color:red;
    }
</style>
