<template>
    <modal @close="close">
        <template v-slot:header>
            <h5 class="modal-title">Place Order</h5>
            <button type="button" class="close" @click="$emit('close')" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </template>

        <template v-slot:body>
            <form method="POST" class="form-horizontal">
                <div class="container">
                    <div class="row">
                        <h5> Fill in the details below:</h5>
                    </div>
                    <div class="row">
                        <div class="col-md-8 col-12">
                            <div id="addressForm" class="card w-100 p-3 mb-3 bg-secondary text-white">
                                <div class="form-group">
                                    <label class="control-label">
                                        First Name <span class="asterisk">*</span>:
                                    </label>
                                    <input type="text" required name="firstname" class="control-input" id="firstname" v-model="enteredAddress['firstname']">
                                </div>
                                <div class="form-group">
                                    <label class="control-label">
                                        Surname <span class="asterisk">*</span>:
                                    </label>
                                    <input type="text" required name="surname" class="control-input" id="surname" v-model="enteredAddress['surname']">
                                </div>
                                <hr class="bg-white">
                                <div class="form-group">
                                    <label class="control-label">
                                        Address 1 <span class="asterisk">*</span>:
                                    </label>
                                    <input type="text" required name="address1" class="control-input" id="address1" v-model="enteredAddress['address1']">
                                </div>
                                <div class="form-group">
                                    <label class="control-label">
                                        Address 2:
                                    </label>
                                    <input type="text" name="address2" class="control-input" id="address2" v-model="enteredAddress['address2']">
                                </div>
                                <div class="form-group">
                                    <label class="control-label">
                                        Town/City <span class="asterisk">*</span>:
                                    </label>
                                    <input type="text" required name="town" class="control-input" id="town" v-model="enteredAddress['town']">
                                </div>
                                <div class="form-group">
                                    <label class="control-label">
                                        Postcode <span class="asterisk">*</span>:
                                    </label>
                                    <input type="text" required name="postcode" class="control-input" id="postcode" v-model="enteredAddress['postcode']">
                                </div>
                                <div class="form-group">
                                    <label class="control-label">
                                        Country <span class="asterisk">*</span>:
                                    </label>
                                    <label class="control-label">
                                        UK <i data-toggle="tooltip" data-placement="right" title="" class="fa fa-info-circle" data-original-title="Sorry- only UK orders are possible at the moment."></i>
                                    </label>
                                </div>
                                <hr class="bg-white">
                                <div class="form-group">
                                    <label class="control-label">
                                        Email <span class="asterisk">*</span>:
                                    </label>
                                    <input type="text" required name="email" class="control-input" id="email" v-model="enteredAddress['email']">
                                </div>
                                <div class="form-group">
                                    <label class="control-label">
                                        Phone <span class="asterisk">*</span>:
                                    </label>
                                    <input type="text" name="phone" class="control-input" id="phone" v-model="enteredAddress['phone']">
                                </div>
                            </div>
                        </div> 
                        <div class="col-md-4 col-12">
                            <div id="orderForm" class="card w-100 p-3 mb-3 bg-secondary text-white">
                                <div class="form-group">
                                    <label class="control-label" for="orderQty">
                                        Quantity:
                                    </label>
                                    <input type="number" name="orderQty" class="control-input ml-2" id="orderQty" v-model="orderQty" min="1" max="10">
                                </div>
                                <div class="form-group">   
                                    <label class="control-label">
                                        Price:
                                    </label>
                                    <label class="control-label ml-2">
                                        {{ (tShirtCost * orderQty) | toCurrency }}
                                    </label>
                                </div>
                                <div class="form-group">   
                                    <label class="control-label">
                                        Delivery:
                                    </label>
                                    <label class="control-label ml-2">
                                        {{ deliveryCost | toCurrency }}
                                    </label>
                                </div>
                                <div class="form-group total text-right">   
                                    <label class="control-label">
                                        Total:
                                    </label>
                                    <label class="control-label ml-2">
                                        {{ totalCost | toCurrency }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group"> 
                                <button id="checkout-button" @click="saveTShirt" type="button" :disabled="!formIsComplete()" class="btn btn-success form-control">
                                    Proceed to Checkout
                                </button>
                            </div>   
                        </div> 
                    </div> 
                </div>
            </form>
            <input id="tshirtId" type="hidden" :value="tshirtId">
        </template>

        <template v-slot:footer>
            <button type="button" class="btn btn-default" @click="$emit('close')">Close</button>
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
            includeBorder: Boolean
        },
        components: {
            modal
        },
        data() {
            return {
                orderQty: 1,
                tShirtCost: 5.50,
                standardDeliveryCost: 5.99,
                enteredAddress: [],
                tshirtId: 0
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
            formIsComplete: function(){
                if (!this.enteredAddress['firstname']) return false;
                if (!this.enteredAddress['surname']) return false;
                if (!this.enteredAddress['address1']) return false;
                if (!this.enteredAddress['town']) return false;
                if (!this.enteredAddress['postcode']) return false;
                if (!this.enteredAddress['email']) return false;
                if (!this.enteredAddress['phone']) return false;
                return true;
            },
            saveTShirt: function(){
                axios.post('/tshirt/save',{
                    monsterId: this.monsterId,
                    color: this.color,
                    gender: this.gender,
                    size: this.size,
                    includeName: this.includeName,
                    includeBorder: this.includeBorder    
                })
                .then((response) => {
                    this.tshirtId=response.data;
                    console.log(response); 
                })
                .catch((error) => {
                    console.log(error);
                });
            },
            close: function() {
                this.$emit('close');
            },
        },
        computed:{
            deliveryCost: function(){
                if (this.orderQty >= 5){
                    return 0;
                } else {
                    return this.standardDeliveryCost;
                }
            },
            totalCost: function(){
                return (this.tShirtCost * this.orderQty) + this.deliveryCost;
            }
        },
        filters: {
            toCurrency: function (value) {
                if (typeof value !== "number") {
                    return value;
                }
                var formatter = new Intl.NumberFormat('en-GB', {
                    style: 'currency',
                    currency: 'GBP',
                    minimumFractionDigits: 2
                });
                return formatter.format(value);
            },
        }
    }
</script>
<style scoped>
    .btn-info:not(.active){
        background-color:#DDEDFA!important;
    }
    .btn-info:not(.active):hover{
        color:#C0C0C0;
    }
    #orderForm, #addressForm{
        font-size:14px;
    }
    .form-group{
        margin-bottom:0.3rem;
    }
    #orderQty{
        width:60px;
    }
    .total{
        font-size:18pt;
        font-weight:bold;
    }

    #addressForm label{
        width:120px;
    }
    .asterisk{
        color:red;
    }
</style>