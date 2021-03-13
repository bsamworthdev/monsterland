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
                                    <img id="tshirtPreview" class="noshare w-100" :src="'/images/tshirt-' + selectedColor + '.png'">
                                    <img :src="monster.image" class="monsterImage noshare" :class="{'border border-dark border-3':includeBorder}">
                                    <img src="/images/monsterland_logo.png" class="monsterLogo noshare">
                                    <label id="monsterName" v-if="includeName" :style="'color:' + monsterNameColor">
                                        {{ monster.name }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <form>
                                     <form-group class="col-12 pl-0 d-none">
                                        <input id="gender_mens" type="radio" name="gender" value="mens" :checked="selectedGender=='mens'" class="mr-1">
                                        <label for="gender_mens" class="pr-3">
                                            <i class="fa fa-male"></i> Mens
                                        </label>

                                        <input id="gender_womens" type="radio" name="gender" value="womens" :checked="selectedGender=='womens'" class="mr-1">
                                        <label for="gender_womens">
                                            <i class="fa fa-female"></i> Womens
                                        </label>
                                    </form-group>
                                    <form-group class="col-12">
                                        <label>Size:</label>
                                        <select class="form-control mb-3" v-model="selectedSize" @change="sizeChanged">
                                            <option value="XXL">XXL</option>
                                            <option value="XL">XL</option>
                                            <option value="L">L</option>
                                            <option value="M">M</option>
                                            <option value="SM">S</option>
                                        </select>
                                    </form-group>

                                    <form-group>
                                        <label>Colour:</label>
                                        <select class="form-control mb-3" v-model="selectedColor" @change="colorChanged">
                                            <option class="navy" value="navy">Navy</option>
                                            <option class="black" value="black">Black</option>
                                            <option class="darkheather" value="darkheather">Dark Heather</option>
                                            <option class="sportgrey" value="sportgrey">Sport Grey</option>
                                            <option class="white" value="white">White</option>
                                        </select>
                                    </form-group>

                                    <form-group class="col-md-6 col-12">
                                        <label>Include Name:</label>
                                        <label class="switch ml-2">
                                            <input type="checkbox" @change="toggleIncludeName" :checked="includeName">
                                            <span class="slider round" ></span>
                                        </label>
                                    </form-group>

                                    <form-group class="col-md-6 col-12">
                                        <label>Include Border:</label>
                                        <label class="switch ml-2">
                                            <input type="checkbox" @change="toggleIncludeBorder" :checked="includeBorder">
                                            <span class="slider round" ></span>
                                        </label>
                                    </form-group>

                                    <button id="placeStripeOrder" class="mt-3 btn btn-success pull-right btn-block d-none" @click.prevent="activeModal=1;">Looks great, continue!</button>
                                    <button id="placeOrder" class="mt-3 btn btn-success pull-right btn-block" @click.prevent="designCompleted">Looks great, continue!</button>
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
                        :include-name="includeName"
                        :include-border="includeBorder">
                </t-shirt-order-component>
                <t-shirt-design-code-component
                        v-if="userId==1"
                        v-show="activeModal==2" 
                        @close="activeModal=0"
                        :monster-id="monster.id"
                        :color="selectedColor"
                        :gender="selectedGender"
                        :size="selectedSize"
                        :include-name="includeName"
                        :include-border="includeBorder"
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
            saveDesign: function(){
                if (!this.designCode || this.designHasChanged){
                    this.designCode = this.generateCode();
                     axios.post('/tshirt/save',{
                        monsterId: this.monster.id,
                        color: this.selectedColor,
                        gender: this.selectedGender,
                        size: this.selectedSize,
                        includeName: this.includeName,
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
                    case "darkheather":
                        return '#FFF';
                    default:
                        return '#000';
               }
           }
        },
        data() {
            return {
                activeModal: 0,
                selectedColor: 'white',
                selectedGender: 'mens',
                selectedSize: 'M',
                includeName: false,
                includeBorder: false,
                designCode: '',
                designHasChanged: false
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
        font-size:3.7vw;
        position:absolute;
        left:0;
        right:0;
        top:74%;
        text-align: center;
        font-family:"Nunito", sans-serif;
        font-weight:bold;
    }

    .monsterImage{
        width:30%;
        position:absolute;
        display:block;
        left:0;
        right:0;
        top:34%;
        margin:auto;
        border-radius:14px;
    }

    .monsterLogo{
        width:24%;
        position:absolute;
        display:block;
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

    @media (min-width: 615px) {
        #monsterName{
            font-size:0.9vw;
        }
    }
</style>
