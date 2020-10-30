@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">

                <div class="card-header">
                    <div class="row">
                        <div class="col-6">
                            <h4 class="pull-left">Preview Book</h4>
                        </div>
                        <div class="col-3">
                            <button class="btn btn-info pull-right btn-block" 
                                onclick="location.href='/book/build/{{ $book->group_id }}/{{ $book->id }}';">
                                Back
                            </button>
                        </div>
                        <div class="col-3">
                            <button id="placeOrder" class="btn btn-success pull-right btn-block" onclick="activeModel=1;">Place An Order</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <book-preview-component
                        :monsters ="{{ $monsters }}"
                        book-title = "{{ $book->title }}"
                        :book-id = "{{ $book->id }}"
                        :active-modal = "1"
                        :quantity = "1">
                    </book-preview-component>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="bookId" value="{{ $book->id }}">
</div>
@endsection
<script src="https://js.stripe.com/v3/"></script>
<script type="text/javascript">
    window.onload= function() {
        // Create an instance of the Stripe object with your publishable API key
        var stripe = Stripe('pk_test_51Hha0VEIt1Qs8vIVnWOCLvxD4UAhjzFuMeqHvvI8xCjRAaVoh4NgVVn8oegY9Jkv8C7KLO6cLemwap3b3BsWqfzk00sxS4rr0L');
        var checkoutButton = document.getElementById('checkout-button');
        var orderQty = document.getElementById('orderQty');
        var bookId = document.getElementById('bookId');
        var addressForm = document.getElementById('addressForm');
        var orderDetails = addressForm.getElementsByTagName('input');

        checkoutButton.addEventListener('click', function() {

            //Get payee details
            var details = [];
            for(var i=0; i<orderDetails.length; i++){
                if (orderDetails[i].hasAttribute('required') && !orderDetails[i].value){
                    alert('please complete all required fields');
                    return false;
                };
                details[orderDetails[i].name] = orderDetails[i].value;
            }

            // Create a new Checkout Session using the server-side endpoint you
            // created in step 3.
            fetch('/stripe/create-checkout-session', {
                method: 'POST',
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    quantity: orderQty.value,
                    bookId: bookId.value,
                    details: details
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