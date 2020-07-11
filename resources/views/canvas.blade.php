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