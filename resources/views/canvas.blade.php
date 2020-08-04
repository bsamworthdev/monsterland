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
                            <button class="btn btn-danger btn-block" onclick="cancel(event, {{ $monster->id }}, {{ $logged_in }})">Cancel</button>
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
                        logged_in="{{ $logged_in }}"
                    >
                    </canvas-component>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<script>

    function cancel(e, monster_id, logged_in){
        if(confirm("Do you really want to exit?")){
            this.cancelConfirm(monster_id, logged_in);
        }
    }

    function cancelConfirm(monster_id, logged_in){
        var cancelImagePath = (logged_in ? '/cancelImage' : '/nonauth/cancelImage');
        var homePath = (logged_in ? '/home' : '/nonauth/home');
        $.ajax({
            url: cancelImagePath,
            method: 'POST',      
            data: { 
                'monster_id' : monster_id,
                "_token": "{{ csrf_token() }}"
            },
            success: function(response){
                if (response == 'success'){
                    location.href = homePath;
                }
            },
            error: function(err){
                alert('failure');
            }
        });

        // e.stopPropagation();

    }



</script>