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
            session_id: String
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
                return (monster.in_progress == 1);
            }
        },
        computed: {
            monstersAwaitingBodies: function (){
                return this.monsters.filter(i => (i.status === 'awaiting body'))
            },
            monstersAwaitingLegs: function (){
                return this.monsters.filter(i => (i.status === 'awaiting legs'))
            },
            preventCreate: function (){
                return this.monsterName.length == 0;
            }
        },
        data() {
            return {
                monsterName : ''
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
