<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Example Component</div>

                    <div class="card-body">
                        <h3>Canvas</h3>
                        <ul class="demoToolList">
                            <li>Clear the canvas: <button id="clearCanvasSimpleColors" @click="clearCanvas" type="button">Clear</button></li>
                            <li>
                                <span class="highlight">Choose a colour: </span>
                                <button id="chooseBlackSimpleColors" @click="chooseColor('black')" type="button">Black</button>
                                <button id="choosePurpleSimpleColors" @click="chooseColor('purple')" type="button">Purple</button>
                                <button id="chooseGreenSimpleColors" @click="chooseColor('green')" type="button">Green</button>
                                <button id="chooseYellowSimpleColors" @click="chooseColor('yellow')" type="button">Yellow</button>
                                <button id="chooseBrownSimpleColors" @click="chooseColor('brown')" type="button">Brown</button>
                            </li>
                            <li>
                                <span class="highlight">Choose a size: </span>
                                <button @click="chooseSize('small')" type="button">Small</button>
                                <button  @click="chooseSize('normal')" type="button">Normal</button>
                                <button @click="chooseSize('large')" type="button">Large</button>
                                <button  @click="chooseSize('huge')" type="button">Huge</button>
                            </li>
                        </ul>
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
                this.clickColor.push(this.curColor);
                this.clickSize.push(this.curSize);
            },
            redraw: function() {
                var clickX = this.clickX;
                var clickY = this.clickY;
                var clickDrag = this.clickDrag;
                var context = this.context;
                this.context.clearRect(0, 0, this.context.canvas.width, this.context.canvas.height); // Clears the canvas
                
                // this.context.strokeStyle = this.color['black'];
                // this.context.lineJoin = this.size['normal'];
                // this.context.lineWidth = 5;
                            
                for(var i=0; i < clickX.length; i++) {		
                    this.context.beginPath();
                    if (clickDrag[i] && i){
                        this.context.moveTo(clickX[i-1], clickY[i-1]);
                    } else{
                        this.context.moveTo(clickX[i]-1, clickY[i]);
                    }
                    this.context.lineTo(clickX[i], clickY[i]);
                    this.context.closePath();
                    this.context.strokeStyle = this.color[this.clickColor[i]];
                    this.context.lineWidth = this.size[this.clickSize[i]];
                    this.context.stroke();
                }
            },
            clearCanvas: function(){
                this.context.fillStyle = '#fff'; // Work around for Chrome
                this.context.fillRect(0, 0, this.canvasWidth, this.canvasHeight); // Fill in the canvas with white
                this.clickX = [];
                this.clickY = [];
                this.clickDrag = [];
                this.clickColor = [];
                this.clickSize = [];
            },
            chooseColor: function(colorName) {
                this.curColor = colorName;
            },
            chooseSize: function(sizeName) {
                this.curSize = sizeName;
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
                color:{
                    "black" : "#000000",
                    "purple" : "#cb3594",
                    "green" : "#659b41",
                    "yellow" : "#ffcf33",
                    "brown" : "#986928"
                },
                size:{
                    "small" : "1",
                    "normal" : "3",
                    "large" : "7",
                    "huge" : "10"
                },
                curColor: 'black',
                clickColor:[],
                clickSize: [],
                curSize: "normal",
                radius: 3,
            }
        },
        mounted() {
            var canvasDiv = document.getElementById('canvasDiv');
            var canvas = document.createElement('canvas');
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
    width:616px;
    height:300px;
}
</style>
