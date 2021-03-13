<template>
    <modal @close="close">
        <template v-slot:header>
            <h5 class="modal-title">Your Design Code</h5>
            <button type="button" class="close" @click="$emit('close')" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </template>

        <template v-slot:body>
            <form method="POST" class="form-horizontal">
                <div class="container">
                    <p>This is your unique design code:</p>
                    <div class="alert alert-warning display-3">
                        {{ designCode }}
                        <small v-if="designCodeCopied" class="pl-1 copyLink" @click="copyDesignCode()">
                            <i class="fa fa-check"></i> copied
                        </small>
                        <small v-else class="pl-1 copyLink" @click="copyDesignCode()">
                            <i class="fa fa-copy"></i> copy to clipboard
                        </small>
                    </div>
                    <p>Enter this in the Monsterland Store when you place your order.</p>
                    <a class="btn btn-success btn-lg" href='https://www.etsy.com/uk/listing/979603177/unisex-customised-monster-t-shirt' target="_blank">
                        Go to Store <i class="fas fa-external-link-alt"></i>
                    </a>
                </div>
            </form>
            <input id="tshirtId" type="hidden" :value="tshirtId">
            <input id="monsterId" type="hidden" :value="monsterId">
        </template>

        <template v-slot:footer>
            <button type="button" class="btn btn-default" @click="close">Close</button>
        </template>
    </modal>
</template>

<script>
    import modal from './Modal' ;

    export default {
        props: {
            loggedIn: String,
            monsterId: Number,
            color: String,
            gender: String,
            size: String,
            includeName: Boolean,
            includeBorder: Boolean,
            designCode: String
        },
        components: {
            modal
        },
        data() {
            return {
                tshirtId: 0,
                designCodeCopied: false
            }
        },
        mounted() {
            console.log('Component mounted.');
            $(function () {
                $('[data-toggle="tooltip"]').tooltip()
            })
        },
        methods: { 
            toggleEmailOnComplete: function(){
                // this.emailOnCompleteValue = !this.emailOnCompleteValue;
                this.$emit('toggleEmailOnComplete');
            },
            copyDesignCode() {
                const el = document.createElement('textarea');  
                el.value = this.designCode;                                 
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
                this.designCodeCopied=true;
            },
            close: function() {
                this.$emit('close');
            },
        },
        computed:{

        },
    }
</script>
<style scoped>
    .btn-info:not(.active){
        background-color:#DDEDFA!important;
    }
    .btn-info:not(.active):hover{
        color:#C0C0C0;
    }
    .copyLink{
        font-size:16px;
        color:#74A8DA;
        cursor:pointer;
    }
    .copyLink:hover{
        text-decoration: underline;
    }
    .fa-check{
        color: green;
    }
</style>