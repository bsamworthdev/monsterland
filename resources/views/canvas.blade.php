@extends('layouts.app_canvas')

@section('content')
<div class="container-xl">
    <div class="row justify-content-center">
        <div class="col-sm-12 col-xl-10">
            <div class="card">
                <div class="card-header"> 
                    <div class="row">
                        <div class="col-7">
                            <h4>Name: <b>{{ $monster->name }}</b></h4>
                            <h5>Draw your monster's {{ $segment_name }}</h5>
                        </div>
                        <div class="col-5">
                            <button class="btn btn-danger btn-block" onclick="cancel(event)">Cancel</button>
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
@endsection

<script>
    function cancel(e){
        if(confirm("Do you really want to exit?")){
            this.cancelConfirm();
        }
    }

    function cancelConfirm(e){
        location.href='/home';
        e.stopPropagation();
    }



</script>