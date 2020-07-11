@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header"> 
                    <div class="row">
                        <div class="col-9">
                            <h4>Draw your monster's {{ $segment_name }}</h4>
                        </div>
                        <div class="col-3">
                            <button class="btn btn-danger" onclick="cancelClick()">Cancel</button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <canvas-component
                        segment_name="{{ $segment_name }}"
                        monster="{{ $monster }}"
                    >
                    </canvas-component>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function cancelClick(){
        location.href='/home';
    }
    function myConfirmation() {
        return 'If you leave this page you will lose all your work.';
    }

    window.onbeforeunload = myConfirmation;

</script>
@endsection