@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            @if ($canUseStore)
                <t-shirt-builder-component
                    :monster ="{{ $monster }}"
                    :user-id="{{ $user_id }}">
                </t-shirt-builder-component>
            @else 
                <div class="alert alert-danger">
                    Store not currently available.
                    <br>
                    <a class="btn btn-info mt-4" href="/home">
                        Return to lobby
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
@if ($user_id == 1)
    <script src="https://js.stripe.com/v3/"></script>
    <script type="text/javascript">
        window.onload= function() {
            // Create an instance of the Stripe object with publishable API key
            var stripe = Stripe('pk_live_51Hha0VEIt1Qs8vIViyAmDJbPQovycf2cpuewUF7GED57hPlo3JFTR0qdIhMSfaXU2WyrhlB3r09MuWfqOTxYMrdG00fVm0eqR9');

            var checkoutButton = document.getElementById('checkout-button');
            checkoutButton.addEventListener('click', function() {

                var orderQty = document.getElementById('orderQty');
                var tshirtId = document.getElementById('tshirtId');
                var monsterId = document.getElementById('monsterId');
                var addressForm = document.getElementById('addressForm');
                var orderDetails = addressForm.getElementsByTagName('input');
            
                //Get payee details
                var details = {};
                for(var i=0; i<orderDetails.length; i++){
                    if (orderDetails[i].hasAttribute('required') && !orderDetails[i].value){
                        alert('please complete all required fields');
                        return false;
                    };
                    details[orderDetails[i].name] = orderDetails[i].value;
                }
                //details = JSON.stringify(details);

                // Create a new Checkout Session using the server-side endpoint you
                // created in step 3.
                fetch('/stripe/create-checkout-session', {
                    method: 'POST',
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        quantity: orderQty.value,
                        address: details,
                        tshirtId: tshirtId.value,
                        monsterId: monsterId.value
                    })
                })
                .then(function(response) {
                    return response.json();
                })
                .then(function(session) {
                    return stripe.redirectToCheckout({ sessionId: session.id });
                })
                .then(function(result) {
                    // If `redirectToCheckout` fails due to a browser or network
                    // error, you should display the localized error message to your
                    // customer using `error.message`.
                    if (result.error) {
                        alert(result.error.message);
                    }
                })
                .catch(function(error) {
                    console.log('Error:', error);
                });
            });
        };
    </script>
@endif