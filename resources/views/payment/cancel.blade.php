@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">

                <div class="card-header">
                    <h4>Payment Cancelled</h4>
                </div>
                <div class="card-body">
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