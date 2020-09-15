<template>
    <div class="container" :class="{ 'modal-open': activeModal > 0 }">
        <div class="row justify-content-center">
            <div id="main-container" class="col-md-12">

                <div class="container">
                    <div class="row mb-2">
                        <h4>Start A New Monster...</h4>
                    </div>
                     <div class="row mb-4">
                        <div class="col-md-3">
                            <button class="btn btn-success" @click="openCreateMonsterModal($event)">Create Monster</button>
                        </div>                      
                    </div>
                    <div class="row mb-2">
                        <h4>...or finish someone else's</h4>
                    </div>
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-12">
                                    <h5>Monsters Needing Bodies</h5>
                                </div>                      
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div v-if="monstersAwaitingBodies.length > 0">
                                    <div class="float-left" v-for="monster in monstersAwaitingBodies" :key="monster.id">
                                        <monster-item-component
                                            :monster="monster"
                                            :created-by-user="createdByUser(monster,'body')"
                                            :in-progress="inProgress(monster)"
                                            :logged-in="true"
                                            :user-is-vip="user_is_vip"
                                            :user-id="user_id">
                                        </monster-item-component>
                                    </div>
                                </div>
                                <div v-else>
                                    <i class="noRecords">No monsters here!</i>
                                </div>
                            </div>
                        </div>
                    </div>
                     <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-12">
                                    <h5>Monsters Needing Legs</h5>
                                </div>                      
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div v-if="monstersAwaitingLegs.length > 0">
                                    <div class="float-left" v-for="monster in monstersAwaitingLegs" :key="monster.id">
                                        <monster-item-component
                                            :monster="monster"
                                            :created-by-user="createdByUser(monster, 'legs')"
                                            :in-progress="inProgress(monster)"
                                            :logged-in="true"
                                            :user-is-vip="user_is_vip"
                                            :user-id="user_id">
                                        </monster-item-component>
                                    </div>
                                </div>
                                <div v-else>
                                    <i class="noRecords">No monsters here!</i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
        <create-monster-component
            v-if="activeModal==1" 
            :user_is_vip="user_is_vip"
            @close="activeModal=0" >
        </create-monster-component>
        <div v-if="user_id==1" class="card mt-5">
            <div class="card-body bg-warning">
                <div class="row mt-12">
                    <div class="col-sm-12 mb-1">
                        <button class="btn btn-primary btn-block" title="Unblock locked monsters" @click="unblockLockedMonsters">
                            Unblock locked monsters
                        </button>
                        <button class="btn btn-primary btn-block" title="Unblock locked monsters" @click="createMonsterPngs">
                            Create missing pngs
                        </button>  
                    </div>
                </div>
            </div>
        </div>
        <div v-if="activeModal > 0" class="modal-backdrop fade show"></div>
    </div>

</template>

<script>
    import monsterItemComponent from './MonsterItem' ;
    import createMonsterComponent from './CreateMonster' ;
    export default {
        props: {
            monsters: Array,
            user_id: Number,
            user_is_vip: Number
        },
        components: {
            monsterItemComponent,
            createMonsterComponent
        },
        methods: {
            openCreateMonsterModal: function(){
                this.activeModal = 1;
            },
            createMonster: function(e){
                var monsterName = document.getElementById('monsterName').value;
                e.preventDefault();
                axios.post('/createNewMonster',{   
                    'name' : monsterName         
                })
                .then((response) => {
                    // var url = '/canvas/' + response.data.id;
                    // window.location.href = url;
                    // console.log(response); 
                })
                .catch((error) => {
                    console.log(error);
                });
               
            },
            createdByUser: function (monster, currentSegment){
                for (var i = 0; i < monster.segments.length; i++){
                    if (monster.segments[i].created_by == this.user_id){
                        switch (currentSegment) {
                            case 'body':
                                if (monster.segments[i].segment == 'head'){
                                    return true;
                                }
                            break;
                            case 'legs':
                                if (monster.segments[i].segment == 'body'){
                                    return true;
                                }
                            break;
                        }

                        
                    }
                }
                return false;
            },
            inProgress: function (monster){
                if (monster.in_progress == 1 && !monster.abandoned){
                    return true;
                } else {
                    return false
                }
            },
            unblockLockedMonsters: function() {
                axios.post('/unblockLockedMonsters',{
                    action: 'unblock'           
                })
                .then((response) => {
                    location.reload();
                    console.log(response); 
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            createMonsterPngs: function() {
                axios.post('/createMonsterPngs',{
                    action: 'createpngs'           
                })
                .then((response) => {
                    location.reload();
                    console.log(response); 
                })
                .catch((error) => {
                    console.log(error);
                });
            }
        },
        computed: {
            monstersAwaitingBodies: function (){
                return this.monsters.filter(i => (i.status === 'awaiting body'))
            },
            monstersAwaitingLegs: function (){
                return this.monsters.filter(i => (i.status === 'awaiting legs'))
            }
        },
        data() {
            return {
                activeModal: 0
            }
        },
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>

<style scoped>
.noRecords{
    padding:10px;
}
</style>
