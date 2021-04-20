<template>
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-6">
                    <h4 class="pull-left">Design T-Shirt</h4>
                </div>
                <div class="col-6">
                    <button class="btn btn-info pull-right btn-block" 
                        @click.prevent="backClick()">
                        Back
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div>
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 col-12 mb-3" >
                                <div id="tshirtPreviewContainer">
                                    <img id="tshirtPreview" class="noshare w-100" :src="'/images/tshirt-' + selectedColorNoSpaces + '.png'">
                                    <img :src="monster.image" class="monsterImage noshare" :class="[{'border border-dark border-3':includeBorder}, selectedPosition]">
                                    <img src="/images/monsterland_logo.png" class="monsterLogo noshare">
                                    <label id="monsterName" v-if="includeName" :style="'color:' + monsterNameColor" :class="selectedPosition">
                                        {{ enteredName }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <form class="form-horizontal">
                                     <form-group class="col-12 pl-0">
                                        <input id="gender_mens" type="radio" name="gender" value="mens" v-model="selectedGender" @click="genderChanged" :checked="selectedGender=='mens'" class="mr-1">
                                        <label for="gender_mens" class="pr-3">
                                            <i class="fa fa-male"></i> Mens
                                        </label>

                                        <input id="gender_womens" type="radio" name="gender" value="womens" v-model="selectedGender" @click="genderChanged" :checked="selectedGender=='womens'" class="mr-1">
                                        <label for="gender_womens">
                                            <i class="fa fa-female"></i> Womens
                                        </label>
                                    </form-group>
                                    <form-group class="col-12 d-none">
                                        <label>Size:</label>
                                        <select class="form-control mb-3" v-model="selectedSize" @change="sizeChanged">
                                            <option value="XXL">XXL</option>
                                            <option value="XL">XL</option>
                                            <option value="L">L</option>
                                            <option value="M">M</option>
                                            <option value="SM">S</option>
                                        </select>
                                    </form-group>
                                    <div class="clearfix"></div>
                                    <form-group class="col-12 pl-0">
                                        <div :class="['colorContainer text-center ',{'checked':selectedColor==index}]" v-for="(color,index) in availableColors" :key="index" @click="colorClicked(index)">
                                            <input type="radio" :checked="selectedColor==index" class="btn-check" name="color" :id="'color_' + index" autocomplete="off">
                                            <p class="btn colorBox mb-0" :style="'background-color:' + color" :for="'color_' + index">
                                            </p>
                                            <label style="background-color:white;" class="colorLabel mb-0 d-block">
                                               {{ index }}
                                            </label>
                                        </div>
                                    </form-group>
                                    <form-group class="d-none">
                                        <label class="control-label">Colour:</label>
                                        <select class="form-control mb-3" v-model="selectedColor" @change="colorChanged">
                                            <option class="navy" value="navy">Navy</option>
                                            <option class="black" value="black">Black</option>
                                            <option class="darkheather" value="darkheather">Dark Heather</option>
                                            <option class="purple" value="purple">Purple</option>
                                            <option class="lightpink" value="lightpink">Light Pink</option>
                                            <option class="daisy" value="daisy">Daisy</option>
                                            <option class="forestgreen" value="forestgreen">Forest Green</option>
                                            <option class="red" value="red">Red</option>
                                            <option class="white" value="white">White</option>
                                        </select>
                                    </form-group>
                                    <br style="clear:both">
                                    <form-group>
                                        <label class="control-label mt-4">Image Position:</label>
                                        <select class="form-control mb-3" v-model="selectedPosition" @change="positionChanged">
                                            <option value="high">High</option>
                                            <option value="middle">Middle</option>
                                            <option value="low">Low</option>
                                        </select>
                                    </form-group>
                                    <form-group class="d-block">
                                        <label class="control-label">Include Name:</label>
                                        <label class="switch ml-2">
                                            <input type="checkbox" @change="toggleIncludeName" :checked="includeName">
                                            <span class="slider round"></span>
                                        </label>
                                        <input v-show="includeName" type="text" @keydown="nameChanged" v-model="enteredName" class="mb-4 input-block form-control">
                                    </form-group>

                                    <form-group class="d-block">
                                        <label class="control-label">Include Border:</label>
                                        <label class="switch ml-2">
                                            <input type="checkbox" @change="toggleIncludeBorder" :checked="includeBorder">
                                            <span class="slider round" ></span>
                                        </label>
                                    </form-group>

                                    <button id="placeOrder" class="mt-3 btn btn-success pull-right btn-block" @click.prevent="designCompleted">Looks great, continue!</button>
                                    <button id="placeStripeOrder" class="mt-3 btn btn-success pull-right btn-block d-none" @click.prevent="activeModal=1;">Looks great, continue!</button>
                                    <button id="cancelOrder" class="mt-2 btn btn-danger pull-right btn-block" @click.prevent="backClick()">Cancel</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <t-shirt-order-component
                        v-show="activeModal==1" 
                        @close="activeModal=0"
                        :monster-id="monster.id"
                        :color="selectedColor"
                        :gender="selectedGender"
                        :size="selectedSize"
                        :position="selectedPosition"
                        :include-name="includeName"
                        :include-border="includeBorder">
                </t-shirt-order-component>
                <t-shirt-design-code-component
                        v-show="activeModal==2" 
                        @close="activeModal=0"
                        :design-code="designCode">
                </t-shirt-design-code-component>
                <div v-if="activeModal > 0" class="modal-backdrop fade show"></div>
            </div>
        </div>
     </div>
</template>

<script>
    import tShirtOrderComponent from './TShirtOrder' ;
    import tShirtDesignCodeComponent from './TShirtDesignCode' ;
    export default {
        props: {
            userId: Number,
            monster: Object
        },
        components: {
            tShirtOrderComponent,
            tShirtDesignCodeComponent
        },
        methods: {
            backClick: function(){
                window.location.href='/gallery/' + this.monster.id;
            },
            toggleIncludeName: function(){
                this.includeName = !this.includeName;
                this.designHasChanged = true;
            },
            toggleIncludeBorder: function(){
                this.includeBorder = !this.includeBorder;
                this.designHasChanged = true;
            },
            colorChanged: function(){
                this.designHasChanged = true;
            },
            sizeChanged: function(){
                this.designHasChanged = true;
            },
            positionChanged: function(){
                this.designHasChanged = true;
            },
            nameChanged: function(){
                this.designHasChanged = true;
            },
            genderChanged: function(){
                this.designHasChanged = true;
                this.selectedColor='white';
            },
            colorClicked: function(color){
                this.selectedColor=color;
                this.designHasChanged = true;
            },
            saveDesign: function(){
                if (!this.designCode || this.designHasChanged){
                    this.designCode = this.generateCode();
                     axios.post('/tshirt/save',{
                        monsterId: this.monster.id,
                        color: this.selectedColor,
                        gender: this.selectedGender,
                        size: this.selectedSize,
                        position: this.selectedPosition,
                        includeName: this.includeName,
                        enteredName: this.enteredName,
                        includeBorder: this.includeBorder,
                        designCode: this.designCode  
                    })
                    .then((response) => {
                        this.tshirtId=response.data;
                        this.designHasChanged=false;
                        console.log(response); 
                    })
                    .catch((error) => {
                        console.log(error);
                    }); 
                }
            },
            generateCode: function() {
                var chars = 'ABCDFGHJKMNRTUVWXY0123456789'.split('');
                var result = '';
                for(var i=0; i<6; i++){
                    var x = Math.floor(Math.random() * chars.length);
                    result += chars[x];
                }
                return result;
            },
            designCompleted: function(){
                this.saveDesign();
                this.activeModal=2;
            }
        },
        computed: {
           monsterNameColor: function(){
               switch(this.selectedColor){
                    case "black":
                    case "navy":
                    case "dark heather":
                    case "forest green":
                    case "purple":
                        return '#FFF';
                    default:
                        return '#000';
               }
           },
           selectedColorNoSpaces: function(){
               return this.selectedColor.replace(/\s/g, '');
           },
           availableColors: function(){
               if (this.selectedGender == 'mens'){
                   return this.mensColors;
               } else {
                   return this.womensColors;
               }
           }
        },
        data() {
            return {
                activeModal: 0,
                selectedColor: 'white',
                selectedGender: 'mens',
                selectedSize: 'M',
                selectedPosition: 'middle',
                includeName: false,
                includeBorder: false,
                designCode: '',
                designHasChanged: false,
                enteredName: this.monster.name,
                mensColors: {
                    'white':'#FFFFFF',
                    'navy':'#252B2C',
                    'black':'#000000',
                    'dark heather':'#474949',
                    'sport grey':'#C0C1C5',
                },
                womensColors: {
                    'white':'#FFFFFF',
                    'forest green': '#152010',
                    'navy':'#252B2C',
                    'red':'#99302C',
                    'purple':'#361F4D',
                    'light pink':'#DDB8BA',
                    'black':'#000000',
                    'daisy':'#DFB75B',
                    'sport grey':'#C0C1C5',
                }
            }
        },
        mounted() {
            console.log('Component mounted.')
        },
    }
</script>

<style scoped>

    #tshirtPreviewContainer{
        position:relative;
    }

    #monsterName{ 
        position:absolute;
        left:0;
        right:0;
        top:74%;
        text-align: center;
        font-family:"Nunito", sans-serif;
        font-weight:bold;
    }

    #monsterName.high{
        top:59%;
    }
    #monsterName.middle{
        top:69%;
    }
    #monsterName.low{
        top:79%;
    }

    .monsterImage{
        width:30%;
        position:absolute;
        display:block;
        left:0;
        right:0;
        top:34%;
        margin:auto;
        border-radius:2px;
    }
    .monsterImage.high{
        top:19%;
    }
    .monsterImage.middle{
        top:29%;
    }
    .monsterImage.low{
        top:39%;
    }
    .monsterLogo{
        width:24%;
        position:absolute;
        display:block;
        visibility:hidden;
        left:0;
        right:0;
        top:20%;
        margin:auto;
        border-radius:14px;
    }

    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
        vertical-align:top;
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

    .border-3{
        border-width:5px!important;
    } 
    .btn-check{
        position: absolute;
        clip: rect(0,0,0,0);
        pointer-events: none;
    }
    .colorContainer{
        border:1px solid black;
        border-radius:6px;
        min-width:72px;
        float:left;
        background-color:white;
        padding:3px;
        font-size:12px;
        margin:3px;
        cursor:pointer;
    }
    .colorContainer:first{
        clear:both;
    }
    .colorContainer.checked{
        border:1px solid blue;
        background-color:cornflowerblue;
    }
    .colorContainer.checked .colorLabel{
        background-color:cornflowerblue!important;
    }
    .colorBox{
        height:30px;
        width:60px;
        border:1px solid black;
    }
    .colorLabel{
        cursor:pointer;
    }

    @media only screen and (max-width: 767px) {
        #monsterName{
            font-size:2.2vw;
        }
    }

    @media only screen and (min-width: 768px) {
        #monsterName{
            font-size:0.9vw;
        }
    }
    
</style>
