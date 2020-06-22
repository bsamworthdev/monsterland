<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Example Component</div>

                    <div class="card-body">
                        <h3>Canvas</h3>
                        <div id="canvasDiv" 
                            @mousedown="mouseDown($event)" 
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
                var mouseX = e.pageX - this.offsetLeft;
                var mouseY = e.pageY - this.offsetTop;
                        
                this.paint = true;
                addClick(e.pageX - this.offsetLeft, e.pageY - this.offsetTop);
                redraw();
            },
            mousemove: function(e){
                if(this.paint){
                    addClick(e.pageX - this.offsetLeft, e.pageY - this.offsetTop, true);
                    redraw();
                }
            },
            mouseleave: function(e){
                this.paint = false;
            },
            addClick: function(x, y, dragging) {
                this.clickX.push(x);
                this.clickY.push(y);
                this.clickDrag.push(dragging);
            },
            redraw: function() {
                var context = this.context;
                context.clearRect(0, 0, context.canvas.width, context.canvas.height); // Clears the canvas
                
                context.strokeStyle = "#df4b26";
                context.lineJoin = "round";
                context.lineWidth = 5;
                            
                for(var i=0; i < clickX.length; i++) {		
                    context.beginPath();
                    if (clickDrag[i] && i){
                        context.moveTo(clickX[i-1], clickY[i-1]);
                    } else{
                        context.moveTo(clickX[i]-1, clickY[i]);
                    }
                    context.lineTo(clickX[i], clickY[i]);
                    context.closePath();
                    context.stroke();
                }
            }
        },
        data() {
            return {
                context: null,
                clickX: [],
                clickY: [],
                clickDrag: [],
                paint: ''
            }
        },
        mounted() {
            var canvasDiv = document.getElementById('canvasDiv');
            var canvas = document.createElement('canvas');
            canvas.setAttribute('width', 500); //canvasWidth
            canvas.setAttribute('height', 300); //canvasHeight
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
