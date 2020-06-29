<template>
    <div class="container">
        <div class="row justify-content-center">
            <div id="main-container" class="col-md-12">

                <div class="container">
                    <div class="row mb-2">
                        <button class="btn btn-success col-md-6" :class="{ 'disabled':this.clickX.length == 0 }" @click="save" type="button">Save</button>
                        <button class="btn btn-info col-md-6" @click="clear" type="button">Clear</button>
                    </div>
                </div>

                <div class="container">
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <div class="colorPicker" :title="index" :class="[index, { 'selected':curColor==index }]" v-for="(color,index) in colors" :key="index">
                                <button class="btn" :class="{ 'selected':curColor==index }" :style="'background-color:' + color" @click="chooseColor(index)" type="button"></button>
                            </div>
                        </div>
                        <div id="sizePickerContainer" class="col-md-5">
                            <div class= "sizePicker" :title="'Size:' + index" :class="[index, { 'selected':curSize==index }]" v-for="(size,index) in sizes" :key="index" @click="chooseSize(index)">
                                <div class="" ></div>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <button @click="setTool('eraser')" title="Eraser" class="btn btn-light eraser" :class="{ 'selected':curTool=='eraser' }" type="button">
                                <i class="fa fa-eraser" aria-hidden="true"></i>
                            </button>
                            
                        </div>
                    </div>
                    <div class="row">
                        <div id="canvasDiv" 
                            @mousedown="mouseDown($event)" 
                            @mouseup="mouseUp($event)" 
                            @mousemove="mouseMove($event)" 
                            @mouseleave="mouseLeave($event)">
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            segment_name: String, 
            monster: String
        },
        methods: {
            mouseDown: function(e){
                var mouseX = e.offsetX;
                var mouseY = e.offsetY;
                        
                this.paint = true;
                this.addClick(mouseX, mouseY);
                this.redraw();
            },
            mouseUp: function(e){
                this.paint = false;
            },
            mouseMove: function(e){
                if(this.paint){
                    var mouseX = e.offsetX;
                    var mouseY = e.offsetY;
                    this.addClick(mouseX, mouseY, true);
                    this.redraw();
                }
            },
            mouseLeave: function(e){
                this.paint = false;
            },
            addClick: function(x, y, dragging) {
                this.clickX.push(x);
                this.clickY.push(y);
                this.clickDrag.push(dragging);
                if(this.curTool == "eraser"){
                    this.clickColor.push("white");
                } else{
                    this.clickColor.push(this.curColor);
                }
                this.clickSize.push(this.curSize);
                this.clickTool.push(this.curTool);
            },
            redraw: function() {
                var clickX = this.clickX;
                var clickY = this.clickY;
                var clickDrag = this.clickDrag;
                var context = this.context;
                this.context.clearRect(0, 0, this.context.canvas.width, this.context.canvas.height); // Clears the canvas
                
                this.context.lineJoin = "round";
                            
                for(var i=0; i < clickX.length; i++) {		
                    this.context.beginPath();
                    if (clickDrag[i] && i){
                        this.context.moveTo(clickX[i-1], clickY[i-1]);
                    } else{
                        this.context.moveTo(clickX[i]-1, clickY[i]);
                    }
                    this.context.lineTo(clickX[i], clickY[i]);
                    this.context.closePath();
                    this.context.strokeStyle = this.colors[this.clickColor[i]];
                    this.context.lineWidth = this.sizes[this.clickSize[i]];
                    this.context.stroke();
                }
            },
            clear: function(){
                this.context.fillStyle = '#fff'; // Work around for Chrome
                this.context.fillRect(0, 0, this.canvasWidth, this.canvasHeight); // Fill in the canvas with white
                this.clickX = [];
                this.clickY = [];
                this.clickDrag = [];
                this.clickColor = [];
                this.clickSize = [];
            },
            chooseColor: function(colorName) {
                this.setTool('marker');
                this.curColor = colorName;
            },
            chooseSize: function(sizeName) {
                this.curSize = sizeName;
            },
            setTool: function(toolName){
                this.curTool = toolName;
                if (toolName == 'eraser'){
                    this.curColor = 'none';
                }
            },
            save: function() {
                var canvas = document.getElementById('canvas');
                var dataURL = canvas.toDataURL();

                axios.post('/saveImage',{
                    imgBase64: dataURL,
                    monster_id: this.monsterJSON.id              
                })
                .then((response) => {
                    console.log(response); 
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            createCanvas: function() {
                var canvasDiv = document.getElementById('canvasDiv');
                var mainContainer = document.getElementById('main-container');
                var canvas = document.createElement('canvas');
                this.canvasWidth = mainContainer.offsetWidth - 30;
                this.canvasHeight = this.canvasWidth/3;
                canvas.setAttribute('width', this.canvasWidth);
                canvas.setAttribute('height', this.canvasHeight);
                canvas.setAttribute('id', 'canvas');
                canvasDiv.appendChild(canvas);
                if(typeof G_vmlCanvasManager != 'undefined') {
                    canvas = G_vmlCanvasManager.initElement(canvas);
                }
                this.context = canvas.getContext("2d");
            }
        },
        computed: {
            monsterJSON: function(){
                return JSON.parse(this.monster);
            }
        },
        data() {
            return {
                context: null,
                clickX: [],
                clickY: [],
                clickDrag: [],
                paint: '',
                canvasWidth: 616,
                canvasHeight: 300,
                colors:{
                    "black" : "#000000",
                    "purple" : "#7c40ff",
                    "pink" : "#cb3594",
                    "blue" : "#45b6FE",
                    "dark blue" : "#296d98",
                    "green" : "#659b41",
                    "light green" : "#90ee90",
                    "yellow" : "#ffcf33",
                    "orange" : "#FF8B3D",
                    "red" : "#FF0000",
                    "brown" : "#986928",
                    "grey" : "#C0C0C0",
                    "dark grey" : "#4D4E4F",
                    "white" : "#FFFFFF",
                },
                sizes:{
                    "xs" : "3",
                    "s" : "8",
                    "m" : "10",
                    "l" : "15",
                    "xl" : "50"
                },
                curColor: 'black',
                clickColor:[],
                curSize: "m",
                clickSize: [],
                curTool: "marker",
                clickTool: []
            }
        },
        mounted() {
            this.$nextTick(function () {
                setTimeout(() => this.createCanvas(), 1);
                console.log('Component mounted.')
            })
        }
    }
</script>

<style scoped>
#canvasDiv{
    border: 1px solid black;
    /*width:616px;
    height:300px;*/
}
.colorPicker, .sizePicker {
    display: inline-block;
    margin:2px;
}
.colorPicker .btn{
    border-radius:35px;
    width:35px;
    height:35px;
    border:3px solid black;
    opacity: 0.7;
    cursor:pointer;
}
.colorPicker .btn:hover{
    opacity: 1;
}
.colorPicker.selected .btn {
    border-color: blue;
    opacity:1;
    outline:none;
}
.sizePicker {
    width: 35px;
    height:35px;
    text-align: center;
    border: 2px solid white;
    border-radius:30px;
}
.sizePickerContainer{
    margin-top:auto;
    margin-bottom:auto;
}
.sizePicker div{
    background-color:#C0C0C0;
    display:inline-block;
    vertical-align: middle;
    cursor:pointer;
}
.sizePicker.selected div {
    background-color: #000000;
    border:2px solid blue;
}
.sizePicker.xs div{
    width:7px;
    height:7px;
    border-radius:7px;
}
.sizePicker.s div{
    width:11px;
    height:11px;
    border-radius:11px;
}
.sizePicker.m div{
    width:16px;
    height:16px;
    border-radius:16px;
}
.sizePicker.l div{
    width:22px;
    height:22px;
    border-radius:22px;
}
.sizePicker.xl div{
    width:28px;
    height:28px;
    border-radius:28px;
}

.eraser {
    cursor:pointer;
    padding-top:2px;
    padding-bottom:2px;
    font-size:20px;
}
.eraser.selected{
    border:2px solid blue;
}
</style>
