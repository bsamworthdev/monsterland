<template>
    <div class="container d-flex align-items-center justify-content-end">
        <div>
            <label class="statLabel mb-0">Following: </label>
            <span class="pr-3 pl-1">
                {{ currFollowingCount }}
            </span>
        </div>
        <div>
            <label class="statLabel mb-0">Followers: </label>
            <span class="pr-3 pl-1">
                {{ currFollowersCount }}
            </span>
        </div>
        <div v-if="loggedIn">
            <button style="width:140px;" v-if="currFollowing == 1" :disabled="inProgress" class="btn btn-danger" @click="unfollowUser()">
                Unfollow <i class="fas fa-user-minus"></i> 
            </button>
            <button style="width:140px;" v-else :disabled="inProgress" class="btn btn-success" @click="followUser()">
                Follow <i class="fas fa-user-plus"></i>
            </button>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            user: Object,
            following: Number,
            followingCount: Number,
            followersCount: Number,
            loggedIn: Number
        },
        components: {
        },
        methods: {
            followUser: function(){
                var _this = this;
                this.inProgress = true;
                axios.post('/monsters/followUser',{
                    user_id: this.user.id, 
                    action: 'followUser'
                })
                .then((res) => {
                    _this.currFollowing = 1;
                    _this.currFollowersCount++;
                    _this.inProgress = false;
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            unfollowUser: function(){
                var _this = this;
                this.inProgress = true;
                axios.post('/monsters/unfollowUser',{
                    user_id: this.user.id,
                    action: 'unfollowUser'
                })
                .then((res) => {
                     _this.currFollowing = 0;
                     _this.currFollowersCount--;
                     _this.inProgress = false;
                })
                .catch((error) => {
                    console.log(error);
                });
            }
        },
        computed: {
           
        },
        data(){
            return {
                currFollowing: this.following,
                currFollowingCount: this.followingCount,
                currFollowersCount: this.followersCount,
                inProgress: false
            }
        },
        mounted() {
            console.log('Component mounted.');
        },
    }
</script>
<style scoped>
   
</style>
