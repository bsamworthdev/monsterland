<template>
    <div class="container d-flex align-items-center justify-content-end">
        <div class="row">
            <div class="text-right d-flex align-items-center justify-content-end col-12" :class="{ 'col-lg-3' : showFollowButton, 'col-lg-6' : !showFollowButton}">
                <label class="statLabel mb-0">Following: </label>
                <span class="pr-3 pl-1">
                    {{ currFollowingCount }}
                </span>
            </div>
            <div class="text-right d-flex align-items-center justify-content-end col-12" :class="{ 'col-lg-3' : showFollowButton, 'col-lg-6' : !showFollowButton}">
                <label class="statLabel mb-0">Followers: </label>
                <span class="pr-3 pl-1">
                    {{ currFollowersCount }}
                </span>
            </div>
            <div v-if="showFollowButton" class="col-12 col-lg-6 text-right">
                <button style="width:140px;" v-if="currFollowing == 1" :disabled="inProgress" class="btn btn-danger" @click="unfollowUser()">
                    Unfollow <i class="fas fa-user-minus"></i> 
                </button>
                <button style="width:140px;" v-else :disabled="inProgress" class="btn btn-success" @click="followUser()">
                    Follow <i class="fas fa-user-plus"></i>
                </button>
            </div>
            <div v-else class="col-12 col-lg-6 text-right">
            </div>
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
            loggedIn: Boolean,
            myPage: Number
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
           showFollowButton: function(){
               return this.loggedIn == 1 && this.myPage == 0;
           }
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
