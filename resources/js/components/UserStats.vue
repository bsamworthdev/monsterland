<template> 
    <div class="container text-right">
        <div v-if="showStats" class="btn btn-link" @click="toggleShowStats">
            <i class="fas fa-caret-up"></i> Hide Recent Activity
        </div>
        <div v-else class="btn btn-link" @click="toggleShowStats">
            <i class="fas fa-caret-down"></i> Show Recent Activity
        </div>
        <div v-if="showStats" class="card w-100 bg-light mb-4 text-left">
            <div class="card-body pb-0">
                <div class="row">
                    <div class="col-12 col-lg-6 mb-1">
                        <h4>Comments</h4>
                        <table class="table">
                            <tr v-for="(comment, index) in stats.comments" :key="index">
                                <td>
                                    <div class="container" style="word-break: break-all;">
                                        <div class="row">
                                            <div class="col-6 p-0">
                                                <a :href="'/gallery/' + comment.monster.id">
                                                    {{ comment.monster.name }}
                                                </a>
                                            </div>
                                            <div class="col-3 p-0">
                                                <small v-if="comment.votes > 0" class="text-success"> 
                                                    {{ comment.votes }}
                                                </small>
                                                <small v-else-if="comment.votes < 0" class="text-danger"> 
                                                    {{ comment.votes }}
                                                </small>
                                                <small v-else-if="comment.votes == 0"> 
                                                    {{ comment.votes }}
                                                </small>
                                            </div>
                                            <div class="col-3 p-0 text-right">
                                                <small>{{ comment.created_at_date }}</small>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <small>{{ comment.comment }}</small>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-12 col-lg-6 mb-1">
                        <h4>Ratings</h4>
                        <table class="table">
                            <tr v-for="(rating, index) in stats.ratings" :key="index">
                                <td>
                                    <a :href="'/gallery/' + rating.monster.id">
                                        {{ rating.monster.name }}
                                    </a>
                                    <br>
                                    <small>Rated: {{ rating.rating }}</small>
                                </td>
                                <td class="xs text-right">
                                    <small>{{ rating.created_at_date }}</small>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            user: Object,
            stats: Object,
            isMyPage:{
                default: null,
                format: Number
            }
        },
        methods: {
            toggleShowStats: function(){
                this.showStats = !this.showStats;
            }
        },
        data (){
            return {
                showStats: false
            }
        },
        computed: {
            
        },
        mounted() {
            console.log('Component mounted.');
        }
    }
</script>
<style scoped>
   
</style>
