@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">

                <div class="card-header">
                    <h4>Payment Received</h4>
                </div>
                <div class="card-body">
                    <h1>Thanks for your order!</h1>
                    <p>
                      We appreciate your business!
                      If you have any questions, please email
                      <a href="mailto:admin@monsterland.net">admin@monsterland.net</a>.
                    </p>
                    <button class="btn btn-primary" onclick="backClick();">Return to Lobby</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
    function backClick(){
        location.href="/home";
    }
</script>