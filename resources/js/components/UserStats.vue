<template>
    <div class="container">
        <div class="card w-100 border-0">
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <h5>Comments</h5>
                        <table class="table">
                            <tr v-for="(comment, index) in stats.comments" :key="index">
                                <td>
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-6 p-0">
                                                <a :href="'/monsters/' + comment.monster.id">
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
                    <div class="col-6">
                        <h5>Ratings</h5>
                        <table class="table">
                            <tr v-for="(rating, index) in stats.ratings" :key="index">
                                <td>
                                    <a :href="'/monsters/' + rating.monster.id">
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
           stats: Object
        },
        methods: {
            
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
