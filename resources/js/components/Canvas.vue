<template>
    <div class="container-xl">
        <div class="row justify-content-center">
            <div id="main-container" class="col-md-12">

                <div class="container-xl">
                    <div id="mainButtons" class="row mb-2">
                        <button class="btn btn-success col-6" :class="{ 'disabled':clickX.length == 0 }" @click="save" type="button">Save</button>
                        <button class="btn btn-info col-6" @click="clear" type="button">Clear</button>
                    </div>
                </div>

                <div class="container-xl">
                    <div class="row mb-2">
                        <div v-if="useOldColors" class="col-7">
                            <div class="colorPicker" :title="index" :class="[index, { 'selected':curColor==index , 'newRow':index=='yellow'}]" v-for="(color,index) in oldColors" :key="index">
                                <button class="btn" :class="{ 'selected':curColor==index }" :style="'background-color:' + color" @click="chooseColor(index)" type="button"></button>
                            </div>
                        </div>
                        <div v-else class="col-7">
                            <div class="colorPicker" :title="index" :class="[index, { 'selected':curColor==index , 'newRow':index=='green'}]" v-for="(color,index) in colors" :key="index">
                                <button class="btn" :class="{ 'selected':curColor==index }" :style="'background-color:' + color" @click="chooseColor(index)" type="button"></button>
                            </div>
                        </div>
                        <div id="sizePickerContainer" class="col-3">
                            <div class= "sizePicker" :title="'Size:' + index" :class="[index, { 'selected':curSize==index }]" v-for="(size,index) in sizes" :key="index" @click="chooseSize(index)">
                                <div class="" ></div>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="btn-group">
                                <button @click="undo()" title="Undo" :disabled="dotCounts == 0" class="btn btn-light undo" type="button">
                                    <i class="fa fa-undo" aria-hidden="true"></i>
                                </button>
                                <button @click="redo()" title="Redo" :disabled="undoneDotCounts == 0" class="btn btn-light redo" type="button">
                                    <i class="fa fa-redo" aria-hidden="true"></i>
                                </button>
                            </div>
                            <div class="btn-group">
                                <button @click="setTool('eyedropper')" title="Pick Color" class="btn btn-light eyedropper" :class="{'active': eyedropperActive}" type="button">
                                    <i class="fas fa-eye-dropper" aria-hidden="true"></i>
                                </button>
                                <button @click="setTool('eraser')" title="Eraser" class="btn btn-light eraser" :class="{ 'selected':curTool=='eraser' }" type="button">
                                <i class="fa fa-eraser" aria-hidden="true"></i>
                            </button>
                            </div>
                        </div>
                    </div>
                    <div id="canvasContainer" :class="['row', {'hasDarkBg':['#ee0000', '#df5300', '#845220', '#fe6161', '#8e16d8', '#e738bc', '#eb4e95', '#0000ff'].includes(curBgColor)}]" :style="{backgroundColor : this.curBgColor}">
                        <img  v-if="segment_name != 'head'" :src="getAboveImage" id="aboveImage">
                        <div v-if="segment_name != 'head'" id="topLine" title="Everything above this line was drawn by the previous artist"></div>
                        <div id="canvasDiv" :class=" segment_name != 'head'? 'includeTopImage' : ''" :style="{cursor: selectedCanvasCursor}"
                             @mousedown="mouseDown($event)" @touchstart="mouseDown($event)"
                             @mouseup="mouseUp($event)" @touchend="mouseUp($event)"
                             @mousemove="mouseMove($event)" @touchmove="mouseMove($event)" 
                             @mouseleave="mouseLeave($event)" @touchleave="mouseLeave($event)" 
                            @keydown.ctrl.26="undo"
                            @keydown.ctrl.25="redo"
                           >
                        </div>
                        <div v-if="segment_name != 'legs'" id="bottomLineLabel">Draw under this line too</div>
                        <div v-if="segment_name != 'legs'" id="bottomLine" title="Everything under this line will be shown to the next artist"></div>
                    </div>
                </div>
                <div class="container-xl mt-3"  v-if="segment_name == 'head'">
                    <div class="row">
                        <div class="col-1 bgColorPicker mb-1 pr-1 pl-1" :title="index" :class="[index, { 'selected':curBgColor==colors[index] , 'newRow':index=='green'}]" v-for="(color,index) in colors" :key="index">
                            <button class="btn btn-block bgColorBtn" :class="{ 'selected':curBgColor==colors[index] }" :style="'background-color:' + color" @click="chooseBgColor(index)" type="button"></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <save-monster-component
            v-if="activeModal==1" 
            @close="activeModal=0"
            @save="saveConfirm"
            @toggleEmailOnComplete = "toggleEmailOnComplete" 
            :segment-name= "segment_name"
            :logged-in="logged_in">
        </save-monster-component>
        <div v-if="activeModal > 0" class="modal-backdrop fade show"></div>
    </div>
</template>

<script>
    import saveMonsterComponent from './SaveMonster' ;
    export default {
        props: {
            segment_name: String, 
            monster: String,
            logged_in: String
        },
        components: {
            saveMonsterComponent
        },
        methods: {
            mouseDown: function(e){
                var offsets = this.getOffsets(e);
                var mouseX = offsets[0];
                var mouseY = offsets[1];
                if (this.eyedropperActive){
                    this.useEyedropper(mouseX, mouseY);
                } else {
                            
                    this.paint = true;

                    //Prevent redo by clearing undo cache
                    this.undoneDotCounts = [];
                    this.undoneDots = [];

                    this.addClick(mouseX, mouseY);
                    this.redraw();
                }
            },
            useEyedropper: function(mouseX, mouseY){
                    var hex = this.curBgColor; //Default to current background colour
                    var canvas = document.getElementById('canvas');
                    var context = canvas.getContext('2d');
                    // mouseX = this.scale(mouseX);
                    // mouseY = this.scale(mouseY);
                    var p = context.getImageData(mouseX, mouseY, 1, 1).data; 

                    switch (this.segment_name){
                        case 'head':
                            break;
                        case 'body':
                            var topCanvasHeight = 266;
                            break;
                        case 'legs':
                            var topCanvasHeight = 299;
                            break;
                    }

                    var alpha = p[3];
                    if (alpha == 0){
                        if (this.segment_name != 'head' && mouseY < 33){
                            var image = document.getElementById('aboveImage');
                            var topCanvas = document.createElement('canvas');

                            var canvasWidth = image.width;
                            var canvasHeight = topCanvasHeight;
                            // canvasWidth = this.scale(canvasWidth);
                            // canvasHeight = this.scale(canvasHeight);

                            topCanvas.width = canvasWidth;
                            topCanvas.height = canvasHeight;

                            var context = topCanvas.getContext('2d');
                            context.drawImage(image, 0, 0);

                            var q = context.getImageData(0, 0, topCanvas.width, topCanvas.height);
                            mouseY = mouseY + (topCanvasHeight-33);
                            var index = (mouseY * q.width + mouseX) * 4;
                            alpha = q.data[index + 3];
                            if (alpha != 0){
                                hex = "#" + ("000000" + this.rgbToHex(q.data[index], q.data[index + 1], q.data[index + 2])).slice(-6);
                            }
                        } 
                    } else{
                        hex = "#" + ("000000" + this.rgbToHex(p[0], p[1], p[2])).slice(-6);
                    }

                    for (var key in this.colors){
                        if (this.colors[key]==hex){
                            this.curColor = key;
                        }
                    }
                    
                    this.deactivateEyedropper();
            },
            mouseUp: function(e){
                var totalDots = 0;
                if (this.paint){
                    for(var i =0; i < this.dotCounts.length; i++){
                        totalDots += this.dotCounts[i];
                    }
                    this.dotCounts.push(this.clickX.length-totalDots);
                    this.paint = false;
                }
            },
            mouseMove: function(e){
                if (this.eyedropperActive){
                    this.selectedCanvasCursor='crosshair';
                }
                if(this.paint){
                    var offsets = this.getOffsets(e);
                    var mouseX = offsets[0];
                    var mouseY = offsets[1];
                    
                    this.addClick(mouseX, mouseY, true);
                    this.redraw();
                    e.stopPropagation();
                    e.preventDefault();
                }
            },
            getOffsets: function(e){
                var currX;
                var currY;
                if(e.type == "touchstart" || e.type == "touchend" || 
                    e.type == "touchmove" || e.type == "touchleave")
                {
                    // var canvas = document.getElementById('canvas');
                    // let r = canvas.getBoundingClientRect();
                    // // currX = this.undoScale(e.touches[0].clientX - this.scale(r.left));
                    // // currY = this.undoScale(e.touches[0].clientY - this.scale(r.top));
                    // currX = e.touches[0].clientX - r.left;
                    // currY = e.touches[0].clientY - r.top;


                    var rect = e.target.getBoundingClientRect();
                    // currX = e.targetTouches[0].clientX - rect.left;
                    // currY = e.targetTouches[0].clientY - rect.top;
                    currX = this.undoScale(e.targetTouches[0].clientX - rect.left);
                    currY = this.undoScale(e.targetTouches[0].clientY - rect.top);

                }
                else
                {
                    currX = e.offsetX;
                    currY = e.offsetY;
                }

                return [currX, currY];
            },
            mouseLeave: function(e){
                var el = event.toElement || e.relatedTarget;
                if (el){
                    if (el.id == 'topLine' || el.id == 'bottomLine' || el.id == 'bottomLineLabel' || el.id == 'aboveImage') {
                        return;
                    }
                }
                if (this.paint == true){
                    this.mouseUp();
                }
                this.paint = false;
            },
            addClick: function(x, y, dragging) {
                this.clickX.push(x);
                this.clickY.push(y);
                this.clickDrag.push(dragging);
                if(this.curTool == "eraser"){
                    //this.clickColor.push(this.getColorName(this.curBgColor));
                    this.clickColor.push('white');
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
                    if (this.useOldColors){
                        this.context.strokeStyle = this.oldColors[this.clickColor[i]];
                    } else {
                        this.context.strokeStyle = this.colors[this.clickColor[i]];
                    }
                    this.context.lineWidth = this.sizes[this.clickSize[i]];
                    this.context.stroke();
                }
            },
            scale:function(val){ 
                return val * this.zoom;
            },
            undoScale:function(val){
                return val/this.zoom;
            },
            clear: function(){
                if(confirm("Do you really want to clear?")){
                    this.clearConfirm();
                }
            },
            clearConfirm: function() {
                this.context.fillStyle = '#fff'; // Work around for Chrome
                // this.context.fillStyle = '#444444'
                this.context.fillRect(0, 0, this.canvasWidth, this.canvasHeight); // Fill in the canvas with white
                this.clickX = [];
                this.clickY = [];
                this.clickDrag = [];
                this.clickColor = [];
                this.clickSize = [];
                this.dotCounts = [];
                
                //Recreate canvas
                var canvasDiv = document.getElementById('canvasDiv');
                var canvas = document.getElementById('canvas');
                canvasDiv.removeChild(canvas);
                this.createCanvas();
            },
            chooseColor: function(colorName) {
                this.setTool('marker');
                this.curColor = colorName;
                this.deactivateEyedropper();
            },
            chooseSize: function(sizeName) {
                this.curSize = sizeName;
                this.deactivateEyedropper();
            },
            setTool: function(toolName){
                this.curTool = toolName;
                if (toolName == 'eraser'){
                    this.deactivateEyedropper();
                    this.curColor = 'none';
                }
                else if (toolName == 'eyedropper'){
                    this.activateEyedropper();
                }
            },
            activateEyedropper: function(){
                this.eyedropperActive = 1;
                this.selectedCanvasCursor = 'crosshair';
            },
            deactivateEyedropper: function(){
                this.eyedropperActive = 0;
                this.selectedCanvasCursor= 'default';
            },
            save: function(){
                if(this.clickX.length != 0){
                    if (this.unlockSaveButtonTimer == 0){
                        this.activeModal = 1;
                    } else {
                        alert('Too fast! Have you really drawn it properly? Scribbles will get deleted.');
                    }
                }
                // if(confirm("Are you sure you want to save?")){
                //     this.saveConfirm();
                // }
            },
            saveConfirm: function() {

                if (this.segment_name != 'legs' && !this.hasDrawnBelowLine()){
                    alert('Make sure you draw under the dotted line too!');
                    return;
                }

                var canvas = document.getElementById('canvas');
                // this.redraw(true); //Add the background to the canvas before saving

                var dataURL = canvas.toDataURL();
                var savePath = (this.monsterJSON.auth == 1 ? '/saveImage' : '/nonauth/saveImage');
                var homePath = (this.logged_in == 1 ? '/home' : '/nonauth/home');
                
                axios.post(savePath, {
                    imgBase64: dataURL,
                    monster_id: this.monsterJSON.id,
                    email_on_complete: this.emailOnComplete,
                    background: this.curBgColor
                               
                })
                .then((response) => {
                    window.onbeforeunload = '';
                    if (this.segment_name == 'legs'){
                        window.location.href='/gallery/' + this.monsterJSON.id;
                    } else {
                        window.location.href=homePath;
                    }
                    console.log(response); 
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            createCanvas: function() {
                var canvasDiv = document.getElementById('canvasDiv');
                var topLine = document.getElementById('topLine');
                var bottomLine = document.getElementById('bottomLine');
                var bottomLineLabel = document.getElementById('bottomLineLabel');
                var aboveImage = document.getElementById('aboveImage');
                var mainContainer = document.getElementById('main-container');
                var canvas = document.createElement('canvas');
                this.canvasWidth = 800; //mainContainer.offsetWidth - 30;
                this.canvasHeight = this.canvasWidth/3;

                if (canvasDiv.classList.contains('includeTopImage')){
                    this.canvasHeight += 33;
                };
                canvasDiv.classList.add('loaded');
                
                canvas.setAttribute('width', this.canvasWidth);
                canvas.setAttribute('height', this.canvasHeight);
                canvas.setAttribute('id', 'canvas');
                //canvas.setAttribute('tabindex', 0);

                canvasDiv.appendChild(canvas);
                if (topLine){
                    topLine.style.width =this.canvasWidth + 'px';
                    topLine.style.display = 'block';
                }
                if (bottomLine) {
                    bottomLine.style.width =this.canvasWidth + 'px';
                    bottomLine.style.display = 'block';
                    bottomLineLabel.style.left = canvas.offsetLeft + 5 + 'px';
                    bottomLineLabel.style.display = 'block';
                }
                if (aboveImage) {
                    aboveImage.style.width =this.canvasWidth + 'px';
                    aboveImage.style.display = 'block';
                }
                if(typeof G_vmlCanvasManager != 'undefined') {
                    canvas = G_vmlCanvasManager.initElement(canvas);
                }
                this.context = canvas.getContext("2d");
                // this.context.fillStyle = '#444444';
                // this.context.fillRect(0, 0, this.canvasWidth, this.canvasHeight);
            },
            hasDrawnBelowLine: function() {
                var clickY = this.clickY;
                var canvas = document.createElement('canvas');
                var found = false;
                            
                for(var i=0; i < clickY.length; i++) {		
                    if ((this.context.canvas.height - clickY[i])< 33) {
                        found = true;
                        break;
                    }
                }
                return found;
            },
            undo: function(){
                var dotCounts = this.dotCounts;
                var dotCount = dotCounts[dotCounts.length-1];
                
                var totalDotCount = 0;
                for(var i=0; i < dotCounts.length; i++) {	
                    totalDotCount += dotCounts[i];
                }

                for(var i = totalDotCount-1; i >= (totalDotCount - dotCount); i--) {	

                    this.undoneDots.push({
                        "clickX" : this.clickX[i],
                        "clickY" : this.clickY[i],
                        "clickDrag" : this.clickDrag[i],
                        "clickColor" : this.clickColor[i],
                        "clickSize" : this.clickSize[i],
                        "clickTool" : this.clickTool[i]
                    });

                    this.clickX.pop();
                    this.clickY.pop();
                    this.clickDrag.pop();
                    this.clickColor.pop();
                    this.clickSize.pop();
                    this.clickTool.pop();
                }
                this.undoneDotCounts.push(dotCount);
                this.dotCounts.pop();
                this.redraw();
            },
            redo: function(){

                var dotCounts = this.dotCounts;
                var undoneDotCounts = this.undoneDotCounts;
                var undoneDotCount = undoneDotCounts[undoneDotCounts.length-1];
                //var dotCount = this.dotCounts[this.dotCounts.length-1];

                var totalUndoneDotCount = 0;
                for(var i=0; i < undoneDotCounts.length; i++) {	
                    totalUndoneDotCount += undoneDotCounts[i];
                }
                
                var undoneDot;
                for(var i = totalUndoneDotCount - 1; i >= totalUndoneDotCount - undoneDotCount ; i--) {
                    undoneDot = this.undoneDots[i];
                    this.clickX.push(undoneDot["clickX"]);
                    this.clickY.push(undoneDot["clickY"]);
                    this.clickDrag.push(undoneDot["clickDrag"]);
                    this.clickColor.push(undoneDot["clickColor"]);
                    this.clickSize.push(undoneDot["clickSize"]);
                    this.clickTool.push(undoneDot["clickTool"]);
                }
                this.undoneDotCounts.pop();
                for(var i=0; i < undoneDotCount; i++){
                    this.undoneDots.pop();
                }
                dotCounts.push(undoneDotCount);
                this.redraw();
            },
            toggleEmailOnComplete: function(){
                this.emailOnComplete = !this.emailOnComplete;
            },
            rgbToHex: function(r, g, b) {
                if (r > 255 || g > 255 || b > 255)
                    throw "Invalid color component";
                return ((r << 16) | (g << 8) | b).toString(16);
            },
            decrementTimer: function(){
                if (this.unlockSaveButtonTimer > 0){
                    this.unlockSaveButtonTimer--;
                    setTimeout(() => this.decrementTimer(), 1000);
                }
            },
            chooseBgColor: function(color){
                this.curBgColor = this.colors[color];
            },
            getColorName: function(colorHex){
                var arr = this.colors;
                var index = Object.keys(arr).find(key => arr[key] === colorHex);
                return index;
            },
            handleOrientationChange: function(){
                this.zoom = screen.availWidth/1000 < 1 ? screen.availWidth/1000 : 1;
            }
        },
        computed: {
            monsterJSON: function(){
                return JSON.parse(this.monster);
            },
            getAboveImage: function(){
                var segments = this.monsterJSON.segments_with_images;
                switch (this.segment_name) {
                    case 'body':
                        for(var i=0; i<segments.length; i++){
                            if (segments[i].segment == 'head') {
                                return segments[i].image;
                            }
                        }
                        break;
                    case 'legs':
                        for(var i=0; i<segments.length; i++){
                            if (segments[i].segment == 'body') {
                                return segments[i].image;
                            }
                        }
                        break;
                }
                return '';
            },
            useOldColors: function() {
                var d1 = new Date(this.monsterJSON.created_at);
                var d2 = new Date('2020-07-30 12:00:00');
                if (d1 < d2){
                    return true;
                } else {
                    return false;
                }
            },
        },
        data() {
            return {
                context: null,
                clickX: [],
                clickY: [],
                clickDrag: [],
                dotCounts: [],
                paint: '',
                canvasWidth: 616,
                canvasHeight: 300,
                oldColors:{
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
                colors:{
                    "black" : "#000000",
                    "dark gray" : "#525252",
                    "medium gray" : "#9f9f9f",
                    "light gray" : "#c1c1c1",
                    "brown" : "#845220",
                    "light brown" : "#cd8d41",
                    "tan" : "#f8d2a7",
                    "yellow" : "#ffff00",
                    "dark yellow" : "#ffd300",
                    "orange" : "#f4a500",
                    "dark orange" : "#df5300",
                    "blueish green" : "#2cb498",
                    "green" : "#2cb443",
                    "light green" : "#00f200",
                    "blue" : "#0000ff",
                    "medium blue" : "#6e6eff",
                    "light blue" : "#b4b4ff",
                    "pink" : "#eb4e95",
                    "light pink" : "#fe8ec6",
                    "purple" : "#8e16d8",
                    "light purple" : "#e738bc",
                    "red" : "#ee0000",
                    "light red" : "#fe6161",
                    "white" : "#FFFFFF",
                },
                sizes:{
                    "xs" : "3",
                    "s" : "8",
                    "m" : "10",
                    "l" : "20",
                    "xl" : "50"
                },
                curColor: 'black',
                clickColor:[],
                curSize: "m",
                clickSize: [],
                curTool: "marker",
                clickTool: [],
                undoneDots: [],
                undoneDotCounts: [],
                activeModal: 0,
                emailOnComplete: 0,
                eyedropperActive: 0,
                selectedCanvasCursor: 'default',
                unlockSaveButtonTimer: 20,
                curBgColor: '#FFFFFF',
                zoom: 1
            }
        },
        mounted() {
            this.$nextTick(function () {
                setTimeout(() => this.createCanvas(), 1000);
            })
            window.addEventListener(
                "orientationchange",
                this.handleOrientationChange
            );
            this.zoom = screen.availWidth/1000 < 1 ? screen.availWidth/1000 : 1;
            this.curBgColor = this.monsterJSON.background;
            this.decrementTimer();
        }
    }
</script>

<style scoped>

#main-container{
    min-height: 300px;
}
#canvasContainer{
    justify-content:center;
    width:800px;
    margin-left:auto;
    margin-right:auto;
    position:relative;
}
#canvasContainer.hasDarkBg #bottomLineLabel {
    color:#E8E8E8;
}
#canvasContainer.hasDarkBg #topLine,
#canvasContainer.hasDarkBg #bottomLine{
    border-bottom:1px dotted #E8E8E8;
}
#canvasDiv{
    z-index:1;
    /*width:616px;
    height:300px;*/
}
#canvasDiv.loaded{
    border: 1px solid black;
}
.sizePicker {
    display: inline-block;
    margin:1px;
}
.colorPicker{
    float: left;
    padding:2px;
}
.colorPicker.newRow{
    clear: left;
}
.colorPicker .btn{
    border-radius:32px;
    width:32px;
    height:32px;
    border:3px solid black;
    opacity: 0.7;
    cursor:pointer;
}
.btn:hover{
    opacity: 1;
}
.colorPicker.selected .btn {
    border-color: blue;
    opacity:1;
    outline:none;
}
.bgColorBtn{
    height:22px;
    border:2px solid black;
    opacity: 0.7;
}
.bgColorPicker.selected .btn {
    border-color: blue;
    opacity:1;
    outline:none;
}
.sizePicker {
    width: 30px;
    height:30px;
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
#bottomLine{
    position:absolute;
    bottom:33px;
    border-bottom:3px dotted red;
    display:none;
    opacity:0.4;
    z-index:2;
    pointer-events: none;
}
#bottomLineLabel{
    position:absolute;
    bottom:32px;
    display:none;
    opacity:0.4;
    z-index:2;
    left:10%;
    color:red;
    pointer-events: none;
}
#topLine{
    position:absolute;
    margin-top:33px;
    border-bottom:3px dotted red;
    display:none;
    opacity:0.4;
    z-index:2;
    pointer-events: none;
}
#aboveImage{
    position:absolute;
    object-fit:none;
    object-position:0% 100%;
    height: 33px;
    display:none;
    z-index:1;
}
#bottomLine,#bottomLineLabel, #topLine, #aboveImage{
    -webkit-user-drag: none;
    -khtml-user-drag: none;
    -moz-user-drag: none;
    -o-user-drag: none;
    -khtml-user-select: none;
    -o-user-select: none;
    -moz-user-select: none;
    -webkit-user-select: none;
    user-select: none;
}

.btn.undo, .btn.redo, .btn.eraser, .btn.eyedropper{
    padding-left:10px;
    padding-right:10px;
    padding-top:5px;
    padding-bottom:5px;
}
.btn.eyedropper.active{
    border:1px solid blue;
    opacity:1;
    outline:none;
}
/*@media only screen and (max-width: 600px) {
    #canvasDiv{
        transform:scaleX(0.3) scaleY(0.3);
        transform-origin:top left;
    }
}*/
@media (max-width: 978px) {
    #mainButtons{
        margin-bottom:3rem!important;
    }
}

</style>
