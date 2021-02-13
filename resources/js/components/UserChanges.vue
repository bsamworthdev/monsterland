<template>
  <div class="latest_changes">
    <table class="table w-100 ">
        <tr v-for="(change, index) in allChanges" :key="index" :class="{'justAdded':isRecent(change.created_at)}">
            <td>
              <small>{{ tidyDate(change.created_at)}}</small>
            </td>
            <td v-if="change.type=='segment_completed' || change.type=='comment'">
              <a v-if="change.user" class="position:absolute" style="max-width: 7rem" :href="'/monsters/' + change.user.id">
                {{ user_id==change.user.id ? 'You' : change.user.name }}
                
              </a>
              <span v-else>GUEST</span>
              {{ change.action }}
              <a v-if="change.type=='segment_completed'" class="position:absolute" style="max-width: 7rem" :href="'/canvas/' + change.monster.id">
                {{ change.monster.name }}
              </a>
              <a v-else-if="change.type=='comment'" class="position:absolute" style="max-width: 7rem" :href="'/gallery/' + change.monster.id">
                {{ change.monster.name }}
              </a>
            </td>
            <td v-else-if="change.type=='monster_completed'">
              {{ change.action }}
              <a class="position:absolute" style="max-width: 7rem" :href="'/gallery/' + change.monster.id">
                {{ change.monster.name }}
              </a>
            </td>
            <td v-else-if="change.type=='rating'">
              <a class="position:absolute" style="max-width: 7rem" :href="'/gallery/' + change.monster.id">
                {{ change.monster.name }}
              </a>
              {{ change.action }}
            </td>
        </tr>
    </table>
  </div> 
</template>

<script>
    export default {
        props: {
          user_id: Number,
          changes: Array
        },
        methods: {
          update: function() {
              //Get any recent changes
              var _this = this;
              var path = '/getNewUserChanges';
              axios.get(path).then(function(response) {
                if (response.body) {
                  var arr = response.body.concat(_this.allChanges);
                  arr.length=5;
                  _this.allChanges = arr;
                }
              });
            
          },
          tidyDate:function(date){
              var unix_timestamp = Date.parse(date);
              var seconds = Math.floor((new Date() - unix_timestamp) / 1000);
              var interval = seconds / 31536000;

              if (interval >= 1) {
                interval =Math.floor(interval);
                return interval + " year" + (interval == 1 ? '' : 's') + " ago";
              }
              interval = seconds / 2592000;
              if (interval >= 1) {
                interval =Math.floor(interval);
                return interval + " month" + (interval == 1 ? '' : 's') + " ago";
              }
              interval = seconds / 86400;
              if (interval >= 1) {
                interval =Math.floor(interval);
                return interval + " day" + (interval == 1 ? '' : 's') + " ago";
              }
              interval = seconds / 3600;
              if (interval >= 1) {
                interval =Math.floor(interval);
                return interval + " hour" + (interval == 1 ? '' : 's') + " ago";
              }
              interval = seconds / 60;
              if (interval >= 1) {
                interval =Math.floor(interval);
                return interval + " min" + (interval == 1 ? '' : 's') + " ago";
              }
              return "Just now";
          },
          isRecent: function(date){
            var unix_timestamp = Date.parse(date);
            var seconds = Math.floor((new Date() - unix_timestamp) / 1000);
            return (seconds < 10);
          }
        },
        computed: {

        },
        data() {
            return {
                allChanges: this.changes
            }
        },
        mounted() {
            console.log('Component mounted.')
            
            const self = this;  
            setInterval(function(){
                self.update();
            }, 10000);
        },
    }
</script>

<style scoped>
.table tr:first-of-type td{
  border-top: 0!important;
}
.justAdded{
  background-color:lightgreen;
  transition: background-color 1s linear;
  -moz-transition: background-color 1s linear;  
  -webkit-transition: background-color 1s linear; 
  -ms-transition: background-color 1s linear; 
}

.justAdded {
    background-color: lightgreen;
    animation: fadeout 1s forwards;
    animation-delay: 2s;
    -moz-animation: fadeout 1s forwards;
    -moz-animation-delay: 2s;
    -webkit-animation: fadeout 1s forwards;
    -webkit-animation-delay: 2s;
    -o-animation: fadeout 1s forwards;
    -o-animation-delay: 2s;
}

@keyframes fadeout {
    to {
        background-color:transparent;
    }

}
@-moz-keyframes fadeout {
    to {
        background-color: transparent;
    }
}
@-webkit-keyframes fadeout {
    to {
        background-color: transparent;
    }
}
@-o-keyframes fadeout {
    to {
        background-color: transparent;
    }
}

</style>
