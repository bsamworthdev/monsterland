<template>
    <div class="container">
        <div class="row justify-content-center">
            <div id="main-container" class="col-md-12">

                <div class="container">
                    <div class="row mb-2">
                        <h4>Start A New Monster...</h4>
                    </div>
                     <div class="row mb-4">
                        <div class="col-md-6">
                            <input type="text" id="monsterName" class="form-control input-lg" placeholder="Enter a name..." v-model="monsterName">
                        </div> 
                        <div class="col-md-3">
                            <button class="btn btn-success" :disabled="preventCreate" @click="createMonster($event)">Create</button>
                        </div>                      
                    </div>
                    <div class="row mb-2">
                        <h4>...or finish someone else's</h4>
                    </div>
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-12">
                                    <h5>Monsters Needing Bodies ({{ monstersAwaitingBodies.length }})
                                        <button v-if="autoRefreshExpired" class="btn btn-info btn-sm float-right" @click="refresh">
                                            <i class="fas fa-sync-alt"></i> Refresh
                                        </button>
                                    </h5>
                                </div>                      
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div v-if="monstersAwaitingBodies.length > 0">
                                    <div class="float-left" v-for="monster in monstersAwaitingBodies" :key="monster.id">
                                        <monster-item-component
                                            :monster="monster"
                                            :created-by-user="createdByUser(monster)"
                                            :in-progress="inProgress(monster)"
                                            :logged-in="false">
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
                                    <h5>Monsters Needing Legs ({{ monstersAwaitingLegs.length }})
                                        <button v-if="autoRefreshExpired" class="btn btn-info btn-sm float-right" @click="refresh">
                                            <i class="fas fa-sync-alt"></i> Refresh
                                        </button>
                                    </h5>
                                </div>                      
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div v-if="monstersAwaitingLegs.length > 0">
                                    <div class="float-left" v-for="monster in monstersAwaitingLegs" :key="monster.id">
                                        <monster-item-component
                                            :monster="monster"
                                            :created-by-user="createdByUser(monster)"
                                            :in-progress="inProgress(monster)"
                                            :logged-in="false"
                                            :user-id="0">
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
    </div>
</template>

<script>
    import monsterItemComponent from './MonsterItem' ;
    export default {
        props: {
            monsters: Array,
            session_id: String,
            showMore: Boolean,
            dailyActionCount: Number
        },
        components: {
            monsterItemComponent
        },
        methods: {
            createMonster: function(e){
                var monsterName = document.getElementById('monsterName').value;
                e.preventDefault();
                axios.post('/nonauth/createNewMonster',{   
                    'name' : monsterName         
                })
                .then((response) => {
                    var url = '/nonauth/canvas/' + response.data.id;
                    window.location.href = url;
                    console.log(response); 
                })
                .catch((error) => {
                    console.log(error);
                });
               
            },
            createdByUser: function (monster){
                for (var i = 0; i < monster.segments.length; i++){
                    if (monster.segments[i].created_by_session_id == this.session_id){
                        return true;
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
            refresh: function() {
                var _this = this;

                var path = '/getDailyActionCount';
                axios.get(path).then(function(response) {
                    var count = response.data;

                    if (count != _this.currentDailyActionCount){
                        var path = '/nonauth/fetchMonsters';
                        axios.get(path).then(function(response) {
                            _this.loadedMonsters = response.data;
                        });
                        _this.currentDailyActionCount = count;
                    }
                });   
                
            },
        },
        computed: {
            monstersAwaitingBodies: function (){
                return this.loadedMonsters.filter(i => (i.status === 'awaiting body'))
            },
            monstersAwaitingLegs: function (){
                return this.loadedMonsters.filter(i => (i.status === 'awaiting legs'))
            },
            preventCreate: function (){
                return this.monsterName.length == 0;
            },
            autoRefreshExpired: function(){
                return this.refreshCount >= this.refreshCountLimit;
            }
        },
        data() {
            return {
                monsterName : '',
                loadedMonsters: this.monsters,
                refreshCount: 0,
                refreshCountLimit: 10,
                timer: null,
                currentDailyActionCount: this.dailyActionCount
            }
        },
        mounted() {
            console.log('Component mounted.');

            const self = this;  
            this.timer = setInterval(function(){
                 if (self.refreshCount < self.refreshCountLimit){
                    self.refresh();
                    self.refreshCount ++;
                } else {
                    clearInterval(self.timer);
                }
            }, 10000);
        }
    }
</script>

<style scoped>
.noRecords{
    padding:10px;
}
</style>
