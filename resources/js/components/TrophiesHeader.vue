<template>
    <div class="container">
        <div class="trophyContainer" :class="{ 'selected':selectedTrophyStyle=='gold' }" @click="trophyClicked($event,'gold')">
            <i class="fas fa-trophy trophy gold"></i>{{ trophyByColor['gold'].length }}
        </div>
        <div class="trophyContainer" :class="{ 'selected':selectedTrophyStyle=='silver' }" @click="trophyClicked($event,'silver')">
            <i class="fas fa-trophy trophy silver"></i>{{ trophyByColor['silver'].length }}
        </div>
        <div class="trophyContainer" :class="{ 'selected':selectedTrophyStyle=='bronze' }" @click="trophyClicked($event,'bronze')">
            <i class="fas fa-trophy trophy bronze"></i>{{ trophyByColor['bronze'].length }}
        </div>
        <div v-if="showTrophyInfo" class="trophyInfo" :class="selectedTrophyStyle">
            <trophy-info-box
                :trophyStyle="selectedTrophyStyle"
                :trophyInfo="selectedTrophyInfo"
                @close="showTrophyInfo=false"
            >
            </trophy-info-box>
        </div>
    </div>
</template>

<script>
    import trophyInfoBox from './TrophyInfoBox';
    export default {
        props: {
            trophies: Array
        },
        components: {
            trophyInfoBox
        },
        methods: {
            trophyClicked: function (e, style){
                if (this.showTrophyInfo == false){
                    this.selectedTrophyStyle = '';

                    // if (this.trophyByColor[style].length > 0){
                        this.selectedTrophyInfo = this.trophyByColor[style]
                        this.selectedTrophyStyle = style;
                        this.showTrophyInfo = true;
                    // }
                    e.stopPropagation();
                } else {
                    this.selectedTrophyStyle = '';
                    this.showTrophyInfo = false;
                }
            },
            onClick: function () {
                this.selectedTrophyStyle = '';
                this.showTrophyInfo = false;
            },
        },
        computed: {
             trophyByColor: function (){
                var trophyByColor = [];
                var trophies = this.trophies;
                var trophy;
                var color;
                
                trophyByColor['gold'] = [];
                trophyByColor['silver'] = [];
                trophyByColor['bronze'] = [];

                for (var i = 0; i < trophies.length; i++){
                    trophy= trophies[i];
                    color = trophy.trophy_type ? trophy.trophy_type.color : trophy.default_color;
                    trophyByColor[color].push(trophy);
                }
                return trophyByColor;
            }
        },
        data(){
            return {
                showTrophyInfo:false,
                selectedTrophyInfo:[],
                selectedTrophyStyle:''
            }
        },
        mounted() {
            console.log('Component mounted.');
            document.addEventListener('click', this.onClick);
        },
        beforeUnmount() {
            document.removeEventListener('click', this.onClick);
        },
    }
</script>
<style scoped>
    .trophy.gold{ color:gold; }
    .trophy.silver{ color:silver; }
    .trophy.bronze{ color:#cd7f32; }
    .fa-trophy {margin-right:2px!important;}
    .container{
        width: 147px;
        margin-left:0px;
    }
    .navbar .trophyInfo{
        top:60px;
    }
    .trophyInfo{
        position:absolute;
        background-color:#FFF;
        border: 1px solid rgba(0, 0, 0, 0.125);
        border-radius: 0.25rem;
        z-index:999;
        width:250px;
        min-height:100px;
    }
    .trophyInfo.silver{
        margin-left:42px;
    }
    .trophyInfo.bronze{
        margin-left:84px;
    }
    .trophyContainer.selected{
        background-color:rgba(0, 0, 0, 0.1);
    }
    .trophyContainer{
        display:inline-block;
    }
    .fa-trophy {
        font-size: 20px;
        margin-left: 5px;
        margin-right: 5px;
        text-shadow: 0 0 3px #000;
        cursor:pointer;
    }
    @media (max-width: 899px) {
        .trophyInfo{
           top:44px;
        }
    }
    @media (max-width: 576px) {
        .trophyInfo{
            position:absolute!important;
            left:calc(50% - 130px)!important;
            margin:0px!important;
        }
        
    }
     @media screen and (max-width: 374px) {
        .trophy{
            font-size:15px;
        }
        .container{
            justify-content:start!important;
            padding-left:4px;
            padding-right:0px;
            width:120px;
        }
     }
</style>
