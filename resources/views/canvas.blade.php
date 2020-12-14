@extends('layouts.app_canvas')

@section('content')
<div class="container-xl">
    <div class="row justify-content-center">
        <div class="col-sm-12 col-xl-10">
            <div class="card">
                <div class="card-header"> 
                    <div class="row">
                        <div class="col-7">
                            @if ($logged_in && $segment_name == 'head')
                                <h4 id="monsterName" onclick="editName()" style="cursor:pointer;">
                                    Name: <b id="monsterNameValue">{{ $monster->name }}</b>
                                    <small>
                                        <i class="fa fa-pen"></i>
                                    </small>
                                </h4>
                                <h4 id="editMonsterName" class="d-none">
                                    Name: <input id="editedMonsterNameValue" maxlength="20" type="text" value="{{ $monster->name }}">
                                    <button class="btn btn-success btn-sm" style="cursor:pointer;" type="button" onclick="saveName({{ $monster->id }})">
                                        <i class="fa fa-check"></i>
                                    </button>
                                </h4>
                            @else 
                                <h4 id="monsterName">
                                    Name: <b id="monsterNameValue">{{ $monster->name }}</b>
                                </h4>
                            @endif
                            <h5>Draw your monster's {{ $segment_name }}</h5>
                        </div>
                        <div class="col-5">
                            <button class="btn btn-danger btn-block" onclick="cancel(event, {{ $monster->id }}, {{ $logged_in }})">Cancel</button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        @if (!$logged_in)
                        <div class="alert alert-warning pb-0">
                            <ol>
                                <li>Take your time and don't scribble</li>
                                @if ($segment_name != 'head')
                                    <li>Try to match the colour and style from the previous section.</li>
                                @endif
                                @if ($segment_name != 'legs')
                                    <li>Draw under the <?= ($segment_name == 'body' ? 'bottom':'') ?> red line to help the next artist.</li>
                                @endif
                                <li>Don't hold back- the weirder the better.</li>
                            </ol>
                        </div>
                        @endif
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
            <input type="hidden" id="hdnMonsterId" value="{{ $monster->id }}">
            <input type="hidden" id="hdnLoggedIn" value="{{ $logged_in }}">
        </div>
    </div>
</div>
@endsection

<script>

    var cancelled = false;
    function cancel(e, monster_id, logged_in){
        if(confirm("Do you really want to exit?")){
            this.cancelConfirm(monster_id, logged_in);
        }
    }

    function cancelConfirm(monster_id, logged_in){
        var cancelImagePath = (logged_in ? '/cancelImage' : '/nonauth/cancelImage');
        var homePath = (logged_in ? '/home' : '/nonauth/home');
        cancelled = true;
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

    window.onbeforeunload = exitCheck;
    window.onunload = cancelBeforeExit;
    function cancelBeforeExit(){
        var monster_id = $('#hdnMonsterId').val();
        var logged_in = $('#hdnLoggedIn').val();
        this.cancelConfirm(monster_id, logged_in);
    }
    function exitCheck(evt){
        if (!cancelled){
            return "Exit without saving?"
        }
    }

    function editName(){
        $('#monsterName').addClass('d-none');
        $('#editMonsterName').removeClass('d-none');
    }

    function saveName(monster_id){
        var new_name = $('#editedMonsterNameValue').val();

        if (new_name.length == 0) {
            alert('No name entered');
            return;
        }

        $.ajax({
            url: '/updateName',
            method: 'POST',      
            data: { 
                'monster_id' : monster_id,
                'monster_name' : new_name,
                "_token": "{{ csrf_token() }}"
            },
            success: function(response){
                if (response == 'success'){
                    $('#monsterNameValue').text($('#editedMonsterNameValue').val());
                    $('#monsterName').removeClass('d-none');
                    $('#editMonsterName').addClass('d-none');
                }
            },
            error: function(err){
                alert('failed to save');
            }
        });
    }


</script>