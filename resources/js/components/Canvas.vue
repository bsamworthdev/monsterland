<template>
    <div class="container-xl">
        <div class="row justify-content-center">
            <div v-if="idleTimerCount > 600  || abandonded" class="alert alert-danger w-100 text-center">
                Your drawing session has timed out!
                <br>
                <div class="btn btn-info mt-2" v-if="logged_in" @click="reviveDrawing">Wait!! I wasn't done!!!</div>
            </div>
            <div v-if="idleTimerCount > 540 && idleTimerCount <= 600" class="alert alert-warning w-100 text-center">
                WARNING: Your session will expire in {{ (600 - idleTimerCount) }} seconds
            </div>
            <div v-if="salvageMode" class="alert alert-danger">
                Someone else has already picked up this section- sorry.
                <br>
                However, if you really don't want your effort to be wasted you can finish the picture and send it to us anyway.
            </div>
            <div id="main-container" :class="['col-md-12',{'peekMode' : peekMode},{'peeked' : peeked}, {'abandoned' : abandonded}, segment_name+'Segment']">

                <div class="container-xl">
                    <div class="row">
                        <div class="col-9">
                            <div id="mainButtons" class="mb-2">
                                <button v-if="salvageMode" class="btn btn-success col-6" :disabled="clickX.length == 0" @click="salvage" type="button">Send to Admins</button>
                                <button v-else class="btn btn-success col-6" :disabled="clickX.length == 0" @click="save" type="button">Save</button>
                                <button class="btn btn-info col-6" @click="clear" type="button">Clear</button>
                            </div>
                        </div>
                        <div class="col-3">
                            <div id="mainButtons">
                                <div class="custom-control custom-switch mb-2" :title="toolsSwitchTooltip">
                                    <input type="checkbox" name="nsfw" :checked="advancedMode" class="custom-control-input" id="tools" :disabled="!user || !user.is_patron" @click="toggleAdvancedMode">
                                    <label class="custom-control-label" for="tools" :disabled="!user || !user.is_patron">
                                        Advanced Tools
                                        <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="right" :title="toolsSwitchTooltip"></i>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container-xl">
                    <div class="row mb-2">
                        <div class="col-7">
                            <div class="colorContainer">
                                <div class="colorPicker" :title="index" :class="[index, { 'selected':curColor==index}]" v-for="(color,index) in colorsRow1" :key="index">
                                    <button class="btn" :class="{ 'selected':curColor==index }" :style="'background-color:' + color" @click="chooseColor(index)" type="button"></button>
                                </div>
                            </div>
                            <div class="colorContainer">
                                <div class="colorPicker" :title="index" :class="[index, { 'selected':curColor==index}]" v-for="(color,index) in colorsRow2" :key="index">
                                    <button class="btn" :class="{ 'selected':curColor==index }" :style="'background-color:' + color" @click="chooseColor(index)" type="button"></button>
                                </div>
                            </div>
                            <div v-if="user && user.is_patron && advancedMode" class="secret">
                                <label>Pastels <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="right" title="Only available to patrons (like you!)"></i></label>
                                <div class="break"></div> <!-- break -->
                                <div class="colorPicker" :title="index" :class="[index, { 'selected':curColor==index }]" v-for="(color,index) in pastel_colors" :key="index">
                                    <button class="btn" :class="{ 'selected':curColor==index }" :style="'background-color:' + color" @click="chooseColor(index)" type="button"></button>
                                </div>
                            </div>
                            <div v-else-if="Object.keys(pastelColorsPreviouslyUsed).length > 0" class="secret">
                                <label>Pastels <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="right" title="Advanced colours used in previous segment"></i></label>
                                <div class="break"></div> <!-- break -->
                                <div class="colorPicker" :title="index" :class="[index, { 'selected':curColor==index }]" v-for="(color,index) in pastelColorsPreviouslyUsed" :key="index">
                                    <button class="btn" :class="{ 'selected':curColor==index }" :style="'background-color:' + color" @click="chooseColor(index)" type="button"></button>
                                </div>
                            </div>
                        </div>
                        <div id="sizePickerContainer" class="col-3">
                            <div class= "sizePicker" :title="'Size:' + index" :class="[index, { 'selected':curSize==index }]" v-for="(size,index) in sizes" :key="index" @click="chooseSize(index)">
                                <div class="" ></div>
                            </div>
                            <div v-if="(user && user.is_patron && advancedMode) || finelinerPreviouslyUsed" class="secret w-100 text-center d-block mt-0" >
                                <div class= "bonusSizePicker" :title="'Size:' + index" :class="[index, { 'selected':curSize==index }]" v-for="(size,index) in bonus_sizes" :key="index" @click="chooseSize(index)">
                                    <div class="text-nowrap rounded pl-2 pr-2" >
                                        Fineliner 
                                        <i class="fa fa-pen pl-1"></i>

                                        <label v-if="finelinerPreviouslyUsed && !(user && user.is_patron && advancedMode)">
                                            <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="right" title="Fineliner was used in previous segment"></i>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="btn-group">
                                <button @click="undo()" title="Undo" :disabled="dotCounts == 0 || undoneDotCounts.length >= undoLimit" class="btn btn-light undo" type="button">
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
                    <div v-if="user && (user.peek_count>0 || user.has_used_app || user.is_patron)" id="previewPane" class="row" :style="{backgroundColor : curBgColor}">
                        <img :src="getAboveImage" @dragstart="$event.preventDefault()">
                    </div>
                    <div id="canvasContainer" :class="['row', {'hasDarkBg':['#ee0000', '#df5300', '#845220', '#fe6161', '#8e16d8', '#e738bc', '#eb4e95', '#0000ff'].includes(curBgColor)}]" :style="{backgroundColor : curBgColor}">
                        <img  v-if="segment_name != 'head'" :src="getAboveImage" id="aboveImage">
                        <div v-if="segment_name != 'head'" id="topLine" title="Everything above this line was drawn by the previous artist"></div>
                        <div id="canvasDiv" :class=" segment_name != 'head'? 'includeTopImage' : ''" :style="{cursor: selectedCanvasCursor}"
                             @mousedown="mouseDown($event)" @touchstart="mouseDown($event)"
                             @mouseup="mouseUp($event)" @touchend="mouseUp($event)"
                             @mousemove="mouseMove($event)" @touchmove="mouseMove($event)" 
                             @mouseleave="mouseLeave($event)" @touchleave="mouseLeave($event)" 
                           >
                        </div>
                        <div v-if="segment_name != 'legs'" id="bottomLineLabel">Draw under this line too</div>
                        <div v-if="segment_name != 'legs'" id="bottomLine" title="Everything under this line will be shown to the next artist"></div>
                    </div>
                </div>
                <div class="container-xl mt-3"  v-if="segment_name == 'head'">
                    <div class="row">
                        <div class="col-1 bgColorPicker mb-1 pr-1 pl-1" :title="index" :class="[index, { 'selected':curBgColor==availableColors[index] , 'newRow':index=='green'}]" v-for="(color,index) in colors" :key="index">
                            <button class="btn btn-block bgColorBtn" :class="{ 'selected':curBgColor==availableColors[index] }" :style="'background-color:' + color" @click="chooseBgColor(index)" type="button"></button>
                        </div>
                    </div>
                    <div class="row secret" v-if="user && user.is_patron && advancedMode">
                        <label>Pastels <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="right" title="Only available to patrons (like you!)"></i></label>
                        <div class="break"></div> <!-- break -->
                        <div class="col-1 bgColorPicker mb-1 pr-1 pl-1" :title="index" :class="[index, { 'selected':curBgColor==availableColors[index] , 'newRow':index=='green'}]" v-for="(color,index) in pastel_colors" :key="index">
                            <button class="btn btn-block bgColorBtn" :class="{ 'selected':curBgColor==availableColors[index] }" :style="'background-color:' + color" @click="chooseBgColor(index)" type="button"></button>
                        </div>
                    </div>
                </div>
                <div class="container-xl mt-3" v-else-if="user">
                    <div class="row">
                        <button id="stopPeekingBtn" v-if="peekMode" :disabled="user.peek_count==0 && !user.has_used_app && !user.is_patron" class="btn btn-danger btn-block" @click="deactivatePeekMode()" type="button">
                            <i class="fa fa-times"></i>
                            Stop peeking
                        </button>
                        <button id="peekBtn" v-else :disabled="user.peek_count==0 && !user.has_used_app && !user.is_patron" :title="(user.peek_count==0 && !user.has_used_app && !user.is_patron) ? 'You have no more peeks left. Download the app (monsterland.net/mobileapp) or become a patron (patreon.com/monsterlandgame) to get unlimited peeks.' : ''" class="btn btn-info btn-block" @click="peekClicked()" type="button">
                            <i class="fa fa-eye"></i>
                             Peek at {{ segment_name == 'legs' ? ' body' : 'head' }}
                             <br>
                             <small v-if="user.has_used_app || user.is_patron">Unlimited</small>
                             <small v-else>{{ currentPeekCount }} peek{{ (currentPeekCount != 1 ? 's':'') }} remaining</small>
                             <div class="alert alert-danger mt-1" v-if="user.peek_count==0 && !user.has_used_app && !user.is_patron">
                                 You have no more peeks left. <a href="/mobileapp">Download the app</a> or <a href="https://www.patreon.com/monsterlandgame">become a patron</a> to get unlimited peeks.
                            </div>
                        </button>
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
        <open-peek-component
            v-if="activeModal==2" 
            @close="activeModal=0"
            @activatePeekMode="activatePeekMode"
            :user="user"
            :segment-name= "segment_name"
            :logged-in="logged_in">
        </open-peek-component>
        <div v-if="activeModal > 0" class="modal-backdrop fade show"></div>
    </div>
</template>

<script>
    import saveMonsterComponent from './SaveMonster' ;
    import openPeekComponent from './OpenPeek' ;
    export default {
        props: {
            user: Object,
            segment_name: String, 
            monster: String,
            logged_in: String
        },
        components: {
            saveMonsterComponent,
            openPeekComponent
        },
        methods: {
            mouseDown: function(e){
                this.resetIdleTimer();

                var offsets = this.getOffsets(e);
                var mouseX = offsets[0];
                var mouseY = offsets[1];
                if (this.eyedropperActive){
                    this.useEyedropper(mouseX, mouseY);
                } else {
                            
                    this.paint = true;

                    this.undoneDotCounts = [];
                    this.undoneDots = [];

                    this.addClick(mouseX, mouseY);
                    this.storeColor();
                    this.setFinelinerUsed();
                    this.redraw();
                }
            },
            storeColor: function(){
                var color = this.availableColors[this.curColor];
                if (this.colorsUsed.indexOf(color) == -1){
                    this.colorsUsed.push(color);
                }
            }, 
            setFinelinerUsed: function(){
                if (this.curSize == 'xxs'){
                    this.finelinerUsed = true;
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

                    for (var key in this.availableColors){
                        if (this.availableColors[key]==hex){
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
                this.resetIdleTimer();
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
                var _this = this;
                var clickX = _this.clickX;
                var clickY = _this.clickY;

                var clickDrag = _this.clickDrag;
                var context = _this.context;
                context.clearRect(0, 0, context.canvas.width, context.canvas.height); // Clears the canvas
                context.lineJoin = "round";
                            
                for(var i=0; i < clickX.length; i++) {		
                    context.beginPath();
                    if (i && clickDrag[i]){
                        context.moveTo(clickX[i-1], clickY[i-1]);
                    } else{
                        context.moveTo(clickX[i]-1, clickY[i]);
                    }
                    context.lineTo(clickX[i], clickY[i]);
                    context.closePath();
                    // if (this.useOldColors){
                    //     context.strokeStyle = this.oldColors[this.clickColor[i]];
                    // } else {
                        context.strokeStyle = _this.availableColors[_this.clickColor[i]];
                    // }
                    context.lineWidth = _this.availableSizes[_this.clickSize[i]];
                    context.stroke();
                }
            },
            scale:function(val){ 
                return val * this.zoom;
            },
            undoScale:function(val){
                return val/this.zoom;
            },
            clear: function(){
                this.resetIdleTimer();
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
                this.colorsUsed = [];
                this.finelinerUsed = false;
                
                //Recreate canvas
                var canvasDiv = document.getElementById('canvasDiv');
                var canvas = document.getElementById('canvas');
                canvasDiv.removeChild(canvas);
                this.createCanvas();
            },
            chooseColor: function(colorName) {
                this.resetIdleTimer();
                this.setTool('marker');
                this.curColor = colorName;
                this.deactivateEyedropper();
            },
            chooseSize: function(sizeName) {
                this.resetIdleTimer();
                this.curSize = sizeName;
                this.deactivateEyedropper();
            },
            setTool: function(toolName){
                this.resetIdleTimer();
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
                this.resetIdleTimer();
                if(this.clickX.length != 0){
                    if (this.unlockSaveButtonTimer == 0){
                        if (this.sameColorsUsed() || (this.user && this.user.vip)){ 
                            this.activeModal = 1;
                            //scroll to top
                            document.body.scrollTop = 0; // For Safari
                            document.documentElement.scrollTop = 0
                        } else {
                            alert('Not matching! Please try to match your drawing to the section above or it will get deleted.');
                        }
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
                    background: this.curBgColor,
                    colorsUsed: this.colorsUsed,
                    finelinerUsed: this.finelinerUsed          
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

                if (this.undoneDotCounts.length >= this.undoLimit) return;

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
                this.curBgColor = this.availableColors[color];
            },
            getColorName: function(colorHex){
                var arr = this.availableColors;
                var index = Object.keys(arr).find(key => arr[key] === colorHex);
                return index;
            },
            handleOrientationChange: function(){
                //this.zoom = screen.availWidth/1000 < 1 ? screen.availWidth/1000 : 1;
                if (this.isIOS){
                    this.zoom = window.innerWidth/1000 < 1 ? window.innerWidth/1000 : 1;
                } else {
                    this.zoom = screen.availWidth/1000 < 1 ? screen.availWidth/1000 : 1;
                }
            },
            setIsIOS: function(){
                var inBrowser = typeof window !== 'undefined';
                var inWeex = typeof WXEnvironment !== 'undefined' && !!WXEnvironment.platform;
                var weexPlatform = inWeex && WXEnvironment.platform.toLowerCase();
                var UA = inBrowser && window.navigator.userAgent.toLowerCase();
                var ios = (UA && /iphone|ipad|ipod|ios/.test(UA)) || (weexPlatform === 'ios');

                this.isIOS = ios;
            },
            peekClicked: function(){
                if (!this.peekAlreadyUsed){
                    this.activeModal=2;
                } else {
                    this.activatePeekMode();
                }
            },
            activatePeekMode: function(){
                
                if (this.user.has_used_app || this.user.is_patron || !this.peekAlreadyUsed){
                    var _this = this;
                    $.ajax({
                        url: '/peekActivated',
                        method: 'POST',      
                        data: {
                            'monster_id' : this.monsterJSON.id,
                            'monster_segment': this.segment_name,
                            'action': 'peekActivated'
                        },
                        success: function(response){
                            if (response == 'success'){
                                _this.peekMode = true;
                                _this.peeked = true;
                                if (!_this.user.has_used_app && !_this.user.is_patron){
                                    _this.currentPeekCount--;
                                }
                            }
                        },
                        error: function(err){
                            alert('failure');
                        }
                    });
                } else {
                    this.peekMode = true;
                    this.peeked = true;
                }
            },
            deactivatePeekMode: function(){
                this.peekMode = false;
            },
            getPrevSegmentColors: function() {
                var segments = this.monsterJSON.segments_with_images;
                var colors = [];
                switch (this.segment_name) {
                    case 'body':
                        for(var i=0; i<segments.length; i++){
                            if (segments[i].segment == 'head' && segments[i].colors_used) {
                                colors = segments[i].colors_used;
                            }
                        }
                        break;
                    case 'legs':
                        for(var i=0; i<segments.length; i++){
                            if (segments[i].segment == 'body' && segments[i].colors_used) {
                                colors = segments[i].colors_used;
                            }
                        }
                        break;
                }

                if (colors.length > 0) {
                    colors = JSON.parse(colors);
                    //Include background color too
                    var bgColor = this.monsterJSON.background;
                    if (!colors.includes(bgColor)){
                        colors.push(bgColor);
                    }
                }
                return colors;
            },
            sameColorsUsed: function (){
                var thisSegmentColors = this.colorsUsed;
                var prevSegmentColors = this.getPrevSegmentColors();
                if (prevSegmentColors.length == 0) return true;

                var filteredArray = prevSegmentColors.filter(value => thisSegmentColors.includes(value));
                // filteredArray = prevSegmentColors.filter(function(n) {
                //     return thisSegmentColors.indexOf(n) !== -1;
                // });
                return filteredArray.length>0;
            },
            toggleAdvancedMode: function(){
                this.advancedMode = !this.advancedMode;
            },
            updateIdleTimer: function(){
                if (this.idleTimerCount > 600){ //10 minutes
                    var cancelImagePath = (this.logged_in ? '/cancelImage' : '/nonauth/cancelImage');

                    var _this=this;
                    $.ajax({
                        url: cancelImagePath,
                        method: 'POST',      
                        data: {
                            'monster_id' : this.monsterJSON.id
                        },
                        success: function(response){
                            if (response == 'success'){
                            //     window.location.href = homePath;
                                _this.abandonded = true;
                            }
                            _this.cancelIdleTimer();
                        },
                        error: function(err){
                            console.log(err);
                            _this.cancelIdleTimer();
                        }
                    });
                } else {
                    this.idleTimerCount ++;
                }
            },
            resetIdleTimer: function(){
                this.idleTimerCount = 0;
                var updateTimerPath = (this.logged_in ? '/canvas/updateIdleTimer' : '/nonauth/canvas/updateIdleTimer');

                //Update the "last_updated" value for monster
                if (new Date() - this.lastUpdatedTime > 60000){
                    axios.post(updateTimerPath, {
                        monster_id: this.monsterJSON.id,
                        action: 'updateIdleTimer'     
                    })
                    .then((response) => {
                        console.log(response); 
                    })
                    .catch((error) => {
                        console.log(error);
                    });
                    this.lastUpdatedTime = new Date();
                }
            },
            startIdleTimer: function(){
                this.idleTimer = setInterval(this.updateIdleTimer, 1000);
            },
            cancelIdleTimer: function(){
                clearInterval(this.idleTimer);
            },
            reviveDrawing: function(){
                var reviveImagePath = '/reviveImage';

                var _this=this;
                $.ajax({
                    url: reviveImagePath,
                    method: 'POST',      
                    data: {
                        'monster_id' : this.monsterJSON.id,
                        'segment_name' : this.segment_name,
                        'action' : 'reviveImage'
                    },
                    success: function(response){
                        if (response == 'revived'){
                            _this.abandonded = false;
                            _this.resetIdleTimer();
                        } else if ( response == 'unrevived'){
                            _this.abandonded = false;
                            _this.salvageMode = true;
                            _this.resetIdleTimer();
                        }     
                    },
                    error: function(err){
                        console.log(err);
                    }
                });
            },
            salvage: function(){

                if (this.segment_name != 'legs' && !this.hasDrawnBelowLine()){
                    alert('Make sure you draw under the dotted line too!');
                    return;
                }

                var canvas = document.getElementById('canvas');

                var dataURL = canvas.toDataURL();
                var salvagePath = (this.monsterJSON.auth == 1 ? '/salvageImage' : '/nonauth/salvageImage');
                var homePath = '/home';
                
                axios.post(salvagePath, {
                    imgBase64: dataURL,
                    monster_id: this.monsterJSON.id,
                    colorsUsed: this.colorsUsed,
                    finelinerUsed: this.finelinerUsed,
                    segment: this.segment_name             
                })
                .then((response) => {
                    window.onbeforeunload = '';
                    window.location.href=homePath;
                    console.log(response); 
                })
                .catch((error) => {
                    console.log(error);
                });
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
            // useOldColors: function() {
            //     var d1 = new Date(this.monsterJSON.created_at);
            //     var d2 = new Date('2020-07-30 12:00:00');
            //     if (d1 < d2){
            //         return true;
            //     } else {
            //         return false;
            //     }
            // } 
            availableColors: function(){
                var standard_colors = _.clone(this.colors);
                var pastel_colors = _.clone(this.pastel_colors);
                var available_colors = Object.assign(standard_colors, pastel_colors);
                return available_colors;
            },
            availableSizes: function(){
                var standard_sizes = _.clone(this.sizes);
                var bonus_sizes = _.clone(this.bonus_sizes);
                var available_sizes = Object.assign(standard_sizes, bonus_sizes);
                return available_sizes;
            },
            pastelColorsPreviouslyUsed: function(){
                var prevSegmentColors = this.getPrevSegmentColors();
                if (!prevSegmentColors) return null;

                var colors = {};
                for (const property in this.pastel_colors) {
                    var hexColor = this.pastel_colors[property];
                    if (prevSegmentColors.includes(hexColor)){
                        //pastel found
                        var color = {};
                        color[property] = this.pastel_colors[property];
                        Object.assign(colors, color);
                    }
                }
                return colors;  
            },
            finelinerPreviouslyUsed: function(){
                var segments = this.monsterJSON.segments_with_images;
                for(var i=0; i<segments.length; i++){
                    if (segments[i].fineliner_used) {
                        return true;
                    }
                }
                return false;
            },
            toolsSwitchTooltip: function(){
                if (this.user && this.user.is_patron) {
                    return 'Only available to patrons (like you!)';
                } else {
                    return 'Only available to Monsterland patrons. Become a patron here: patreon.com/monsterlandgame';
                }
            },
            colorsRow1: function(){
                var colorCount = 0;
                var colors = {};
                for (const property in this.colors) {
                    if (colorCount == 12) break;
                    var color = {};
                    color[property] = this.colors[property];
                    Object.assign(colors, color);
                    colorCount++;
                }
                return colors;
            },
            colorsRow2: function(){
                var colorCount = 0;
                var colors = {};
                for (const property in this.colors) {
                    if (colorCount >= 12) {
                        var color = {};
                        color[property] = this.colors[property];
                        Object.assign(colors, color);
                    }
                    colorCount++;
                }
                return colors;
            },
            peekAlreadyUsed: function(){
                return (this.currentPeekCount != this.user.peek_count)
            }
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
                pastel_colors:{
                    "pastel red" : "#F4B6BB",
                    "pastel orange" : "#FFBEBC",
                    "pastel yellow" : "#FFFFD1",
                    "pastel dark green" : "#BFFCC6",
                    "pastel green" : "#DBFFD6",
                    "pastel dark blue" : "#6EB5FF",
                    "pastel blue" : "#9BE0FC",
                    "pastel light blue" : "#C4FAF8",
                    "pastel dark purple" : "#A79AFF",
                    "pastel light purple" : "#B5B9FF",
                    "pastel dark pink" : "#FF9CEE",
                    "pastel light pink" : "#FBE4FF"
                },
                sizes:{
                    "xs" : "3",
                    "s" : "8",
                    "m" : "10",
                    "l" : "20",
                    "xl" : "50"
                },
                bonus_sizes:{
                    "xxs" : "1",
                },
                curColor: 'black',
                clickColor:[],
                curSize: "m",
                clickSize: [],
                curTool: "marker",
                clickTool: [],
                undoneDots: [],
                undoneDotCounts: [],
                undoLimit:10,
                activeModal: 0,
                emailOnComplete: 0,
                eyedropperActive: 0,
                selectedCanvasCursor: 'default',
                unlockSaveButtonTimer: 20,
                curBgColor: '#FFFFFF',
                zoom: 1,
                isIOS: false,
                peekMode:false,
                currentPeekCount: this.user ? this.user.peek_count : 0,
                peeked:false,
                colorsUsed: [],
                finelinerUsed: false,
                advancedMode: false,
                isIdle:false,
                idleTimerCount:0,
                abandonded: false,
                lastUpdatedTime : new Date(),
                salvageMode: false
            }
        },
        mounted() {
            this.$nextTick(function () {
                setTimeout(() => this.createCanvas(), 1000);
            })
            $(function () {
                $('[data-toggle="tooltip"]').tooltip()
            })
            window.addEventListener(
                "orientationchange",
                this.handleOrientationChange
            );

            this.setIsIOS();
            if (this.isIOS){
                this.zoom = window.innerWidth/1000 < 1 ? window.innerWidth/1000 : 1;
            } else {
                this.zoom = screen.availWidth/1000 < 1 ? screen.availWidth/1000 : 1;
            }
            //this.zoom = screen.availWidth/1000 < 1 ? screen.availWidth/1000 : 1;
            this.curBgColor = this.monsterJSON.background;
            this.decrementTimer();
            this.startIdleTimer();
        }
    }
</script>

<style scoped>

#main-container{
    min-height: 300px;
    z-index:99;
}
#previewPane{
    display:none;
}
#previewPane img{
    -webkit-user-drag: none;
    -khtml-user-drag: none;
    -moz-user-drag: none;
    -o-user-drag: none;
    -o-user-select: none;
    -moz-user-select: none;
    -webkit-user-select: none;
    -ms-user-select: none;
    user-select: none;
    -webkit-tap-highlight-color: transparent;
}
#main-container.peekMode #canvasDiv.loaded{
    border-top:none!important;
}
#main-container.peekMode #previewPane{
    display:block!important;
    justify-content:center;
    width:802px;
    margin-left:auto;
    margin-right:auto;
    position:relative;
    overflow: hidden;
    border-top:1px solid black;
    border-left:1px solid black;
    border-right:1px solid black;
}
#main-container.peekMode.bodySegment #previewPane{
    max-height:233px;
}

#main-container.peekMode.legsSegment #previewPane{
    max-height:269px;
}

#main-container.peeked{
    background-color:whitesmoke;
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
    width: 30px;
    height:30px;
    text-align: center;
    border: 2px solid white;
    border-radius:30px;
}
.bonusSizePicker{
    display: inline-block;
    margin:1px;
    height:28px;
}
.bonusSizePicker div{
    display:inline-block;
    border:2px solid lightyellow;
    vertical-align: middle;
    cursor:pointer;
}
.bonusSizePicker.selected div {
    border:2px solid blue;
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
    border:3px solid #4B4B4B;
    cursor:pointer;
}
#stopPeekingBtn, #peekBtn{
    opacity:0.7;
}
.btn:enabled:hover{
    opacity: 1!important;
}
.colorPicker.selected .btn {
    opacity:1;
    outline:none;
    border:4px solid blue;

}
.bgColorBtn{
    height:22px;
    border:2px solid #4B4B4B;
}
.bgColorPicker.selected .btn {
    border:4px solid blue;
    opacity:1;
    outline:none;
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

.colorContainer{
    display:flex;
    flex-wrap: wrap;
    padding-top:2px;
    padding-bottom:2px;
    padding-left:5px;
}
.secret{
    border:2px solid black;
    border-radius:10px;
    background-color: lightyellow;
    display:flex;
    flex-wrap: wrap;
    float:left;
    padding-top:5px;
    padding-bottom:5px;
    padding-left:5px;
    margin-top:5px;
}
.secret label{
    flex: 0 0 100%;
    margin:0px;
    margin-top:-4px;
    padding-left:8px;
}
.colorPicker{
    flex:1;
    padding:0px;
}
.secret.row{
    float:none!important;
}
.break {
    flex-basis: 100%;
    height: 0;
}
#main-container.abandoned{
    opacity: 0.4;
    display:none;
}

@media (max-width: 978px) {
    #mainButtons{
        margin-bottom:3rem!important;
    }
}

</style>
