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
                        <div class="col-md-3">
                            <div class= "sizePicker" :title="'Size:' + index" :class="[index, { 'selected':curSize==index }]" v-for="(size,index) in sizes" :key="index" @click="chooseSize(index)">
                                <div class="" ></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button @click="setTool('eraser')" title="Eraser" class="eraser" :class="{ 'selected':curTool=='eraser' }" type="button">
                                <i class="fa fa-eraser" aria-hidden="true"></i> Eraser
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
                    imgBase64: dataURL               
                })
                .then((response) => {
                    console.log('saved' + response); 
                })
                .catch((error) => {
                    console.log(error);
                });
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
                    "purple" : "#cb3594",
                    "blue" : "#0000FF",
                    "green" : "#659b41",
                    "yellow" : "#ffcf33",
                    "red" : "#FF0000",
                    "brown" : "#986928",
                    "white" : "#FFFFFF",
                },
                sizes:{
                    "little" : "1",
                    "normal" : "3",
                    "large" : "7",
                    "huge" : "10"
                },
                curColor: 'black',
                clickColor:[],
                curSize: "normal",
                clickSize: [],
                curTool: "marker",
                clickTool: []
            }
        },
        mounted() {
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
            console.log('Component mounted.')
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
    border:2px solid black;
    opacity: 0.6;
}
.colorPicker.selected .btn {
    border-color: blue;
    opacity:1;
    outline:none;
}
.sizePicker {
    width: 25px;
    height:25px;
    text-align: center;
    border: 2px solid white;
}
.sizePicker div{
    background-color:black;
    display:inline-block;
    vertical-align: middle;
}
.sizePicker.selected {
    border: 2px solid blue;
}
.sizePicker.little div{
    width:3px;
    height:3px;
    border-radius:3px;
}
.sizePicker.normal div{
    width:6px;
    height:6px;
    border-radius:6px;
}
.sizePicker.large div{
    width:8px;
    height:8px;
    border-radius:8px;
}
.sizePicker.huge div{
    width:12px;
    height:12px;
    border-radius:12px;
}
.eraser.selected{
    border:2px solid blue;
}
</style>
