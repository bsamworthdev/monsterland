<template>
    <modal @close="close">
        <div slot="header">
            <button type="button" class="close" @click="$emit('close')" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title">Place Order</h5>
        </div>

        <div slot="body">
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
                                    <input type="text" required name="firstname" class="control-input" id="firstname" value="">
                                </div>
                                <div class="form-group">
                                    <label class="control-label">
                                        Surname <span class="asterisk">*</span>:
                                    </label>
                                    <input type="text" required name="surname" class="control-input" id="surname" value="">
                                </div>
                                <div class="form-group">
                                    <label class="control-label">
                                        Address 1 <span class="asterisk">*</span>:
                                    </label>
                                    <input type="text" required name="address1" class="control-input" id="address1" value="">
                                </div>
                                <div class="form-group">
                                    <label class="control-label">
                                        Address 2:
                                    </label>
                                    <input type="text" name="address2" class="control-input" id="address2" value="">
                                </div>
                                <div class="form-group">
                                    <label class="control-label">
                                        Town/City <span class="asterisk">*</span>:
                                    </label>
                                    <input type="text" required name="town" class="control-input" id="town" value="">
                                </div>
                                <div class="form-group">
                                    <label class="control-label">
                                        Postcode <span class="asterisk">*</span>:
                                    </label>
                                    <input type="text" required style="width:140px" name="postcode" class="control-input" id="postcode" value="">
                                </div>
                                <div class="form-group mt-2">
                                    <label class="control-label">
                                        Email <span class="asterisk">*</span>:
                                    </label>
                                    <input type="text" required name="email" class="control-input" id="email" value="">
                                </div>
                                <div class="form-group mt-2">
                                    <label class="control-label">
                                        Phone <span class="asterisk">*</span>:
                                    </label>
                                    <input type="text" name="phone" class="control-input" id="phone" value="">
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
                                    <label class="control-label">
                                        {{ (bookCost * orderQty) | toCurrency }}
                                    </label>
                                </div>
                                <div class="form-group">   
                                    <label class="control-label">
                                        Delivery:
                                    </label>
                                    <label class="control-label">
                                        {{ deliveryCost | toCurrency }}
                                    </label>
                                </div>
                                <div class="form-group total text-right">   
                                    <label class="control-label">
                                        Total:
                                    </label>
                                    <label class="control-label">
                                        {{ totalCost | toCurrency }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group"> 
                                <button id="checkout-button" type="button" disabled class="btn btn-success form-control">
                                    Proceed to Checkout
                                </button>
                            </div>   
                        </div> 
                    </div> 
                </div>
            </form>
        </div>

        <div slot="footer">
            <button type="button" class="btn btn-default" @click="$emit('close')">Close</button>
        </div>
    </modal>
</template>

<script>
    import modal from './Modal' ;

    export default {
        props: {
            segmentName: String,
            email_on_complete: Boolean,
            loggedIn: String,
            quantity: Number
        },
        components: {
            modal
        },
        data() {
            return {
                orderQty: this.quantity,
                bookCost: 5.50,
                standardDeliveryCost: 5.99
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
                return (this.bookCost * this.orderQty) + this.deliveryCost;
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
        font-size:14pt;
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