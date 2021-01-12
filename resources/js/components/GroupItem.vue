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
                <div v-if="userId == 1" class="statTitle mt-2">
                    <i>Last Edited {{ lastEditedDate }}</i>
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
                <div v-if="completeMonsterCount>10" class="btn btn-info d-none" @click="buildBook()">
                    Create Book
                </div>
            </div>
        </div>                      
    </div>
</template>

<script>
    export default {
        props: {
            group: Object,
            userId: Number
        },
        methods: {
            buildBook: function(){
                location.href = '/book/build/'+ this.group.id;
            },
            tidyDate: function (date) {
                var unix_timestamp = Date.parse(date);
                var seconds = Math.floor((new Date() - unix_timestamp) / 1000);
                var interval = seconds / 31536000;

                if (interval >= 1) {
                    interval = Math.floor(interval);
                    return interval + " year" + (interval == 1 ? '' : 's') + " ago";
                }

                interval = seconds / 2592000;

                if (interval >= 1) {
                    interval = Math.floor(interval);
                    return interval + " month" + (interval == 1 ? '' : 's') + " ago";
                }

                interval = seconds / 86400;

                if (interval >= 1) {
                    interval = Math.floor(interval);
                    return interval + " day" + (interval == 1 ? '' : 's') + " ago";
                }

                interval = seconds / 3600;

                if (interval >= 1) {
                    interval = Math.floor(interval);
                    return interval + " hour" + (interval == 1 ? '' : 's') + " ago";
                }

                interval = seconds / 60;

                if (interval >= 1) {
                    interval = Math.floor(interval);
                    return interval + " min" + (interval == 1 ? '' : 's') + " ago";
                }

                return "Just now";
            },
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
            },
            lastEditedDate: function(){
                var monsters = this.group.monsters;
                var lastEditedDate;
                for(var i = 0; i < monsters.length; i++){
                    if (!lastEditedDate || monsters[i].updated_at > lastEditedDate){
                        lastEditedDate = monsters[i].updated_at;
                    }
                }
                return this.tidyDate(lastEditedDate);
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
