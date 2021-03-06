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
                <div v-if="user.id == 1" class="statTitle mt-2">
                    <i>Last Edited {{ lastEditedDate }}</i>
                </div>
                <div class="statTitle mt-2">
                    <b>Monsters:</b> {{ completeMonsterCount + incompleteMonsterCount }}
                </div>
                <p class="complete">- Complete: {{ completeMonsterCount }}</p>
                <p class="incomplete">- Incomplete: {{ incompleteMonsterCount }}</p>

                <div class="statTitle mt-2" :class="{'copied':codeCopied }" id="codeCopied">
                    <b>
                        Code 
                        <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="Give this code to anyone you want to invite to your group."></i>
                        :
                    </b> {{ group.code }}
                    <i class="fa fa-copy pl-1" @click="copyCode(group.code)" title="copy code"></i>
                    <i class="fa fa-check pl-1" @click="copyCode(group.code)" title="copied to clipboard"></i>
                </div>
                <div class="btn btn-success btn-block mt-1" @click="enterGroup(group.code)">
                    <div class="spinner-border" v-if="enteringGroup" role="status">
                        <span class="sr-only"> Entering...</span>
                    </div>
                    <span v-else>
                        Enter
                        <i class="pl-2 fa fa-arrow-right"></i>
                    </span>
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
            user: Object,
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
            copyCode: function(code) {
                const el = document.createElement('textarea');  
                el.value = code;                                 
                el.setAttribute('readonly', '');                
                el.style.position = 'absolute';                     
                el.style.left = '-9999px';                      
                document.body.appendChild(el);                  
                const selected =  document.getSelection().rangeCount > 0  ? document.getSelection().getRangeAt(0) : false;                                    
                el.select();                                    
                document.execCommand('copy');                   
                document.body.removeChild(el);                  
                if (selected) {                                 
                    document.getSelection().removeAllRanges();    
                    document.getSelection().addRange(selected);   
                }
                this.codeCopied=true;
            },
            enterGroup: function(code){
                this.enteringGroup=true;
                axios.post('/privategroups/entergroup',{   
                    'name': this.user.name + ' (owner)',
                    'group_code': code
                })
                .then((res) => {
                    console.log(res);
                    location.href = '/home';
                })
                .catch((error) => {
                    console.log(error);
                    this.enteringGroup=false;
                });  
            }
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
                codeCopied:false,
                enteringGroup: false
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

    #codeCopied .fa-check{
        display:none;
        color:green;
    }
    #codeCopied .fa-copy{
        cursor:pointer;
        display:inline!important;
    }

    #codeCopied.copied .fa-check{
        display:inline!important;
    }
    #codeCopied.copied .fa-copy{
        display:none!important;
    }
    .spinner-border{
        height:1.2em;
        width:1.2em;
    }
</style>
