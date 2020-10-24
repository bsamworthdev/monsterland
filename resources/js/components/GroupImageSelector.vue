<template>
    <div class="container">
        <div class="row mb-2">
            <div class="col-md-8 col-12">
                <h3> 
                    Select monsters
                    <i>({{ selectedMonsters.length }} selected)</i>
                </h3>
            </div>
            <div class="col-md-4 col-12">
                <button class="btn btn-lg btn-success btn-block" 
                :disabled="selectedMonsters.length<minRequiredMonsters"
                :title="continueButtonTitle" @click="continueClick()">
                    Continue
                </button>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-12">
                <table class="table"> 
                    <tr v-for="(monster, index) in monsters" :key="monster.id" monster_id="monster.id" :class="['monster', {'selected':monsterIsSelected(monster.id)}]">
                        <td>{{ (index + 1) }}</td>
                        <td>
                            <a :href="'/gallery/' + monster.id">{{ monster.name }}</a>
                        </td>
                        <td>
                            <a :href="'/gallery/' + monster.id">
                                <img :src="'/storage/' + monster.id + '.png'">
                            </a>
                        </td>
                        <td>
                            <label class="switch">
                                <input type="checkbox" checked>
                                <span class="slider round" @click="toggleSelected(monster.id)"></span>
                            </label>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            groupId: Number,
            monsters: Array
        },
        methods: {
            toggleSelected: function(monster_id){
                var index = this.selectedMonsters.indexOf(monster_id);
                if (index == -1){
                    this.selectedMonsters.push(monster_id);
                } else {
                    this.selectedMonsters.splice(index, 1);
                }
            },
            monsterIsSelected: function(monster_id){
               return this.selectedMonsters.indexOf(monster_id) > -1
            },
            continueClick: function(){
                axios.post('/book/save',{
                    monsters: this.selectedMonsters             
                })
                .then((response) => {
                    var book_id = response.data;
                    location.href = '/book/preview/'+ book_id;
                    console.log(response); 
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            setSelectedMonsterIds: function(){
                for (var i=0 ; i<this.monsters.length; i++) {
                    this.selectedMonsters.push(this.monsters[i].id);
                }
            }
        },
        computed: {
            continueButtonTitle () {
                if (this.selectedMonsters < this.minRequiredMonsters) {
                    return this.minRequiredMonsters + ' monsters required';
                }
            }
        },
        data() {
            return {
                selectedMonsters: [],
                minRequiredMonsters: 10
            }
        },
        mounted() {
            this.setSelectedMonsterIds();
            console.log('Component mounted.')
        }
    }
</script>

<style scoped>
   img{
       width:50px;
       height:50px;
   }
   .monster{
       font-size:x-large;
   }
   .monster.selected{
       background-color:#e6f7ff;
   }

   .switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 34px;
    }

    .switch input { 
    opacity: 0;
    width: 0;
    height: 0;
    }

    .slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    -webkit-transition: .4s;
    transition: .4s;
    }

    .slider:before {
    position: absolute;
    content: "";
    height: 26px;
    width: 26px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    -webkit-transition: .4s;
    transition: .4s;
    }

    input:checked + .slider {
    background-color: #2196F3;
    }

    input:focus + .slider {
    box-shadow: 0 0 1px #2196F3;
    }

    input:checked + .slider:before {
    -webkit-transform: translateX(26px);
    -ms-transform: translateX(26px);
    transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
    border-radius: 34px;
    }

    .slider.round:before {
    border-radius: 50%;
    }

</style>
