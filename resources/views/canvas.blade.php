@extends('layouts.app_canvas')

@section('content')
<div class="container-xl">
    <div class="row justify-content-center">
        <div class="col-sm-12 col-xl-10">
            <div class="card">
                <div class="card-header"> 
                    <div class="row">
                        <div class="col-5">
                            @if ($logged_in && $segment_name == 'head')
                                <h4 id="monsterName" onclick="editName()" style="cursor:pointer;">
                                    Name: <b id="monsterNameValue">{{ $monster->name }}</b>
                                    <small>
                                        <i class="fa fa-pen ml-1"></i>
                                    </small>
                                </h4>
                                <h4 id="editMonsterName" class="d-none text-nowrap form-inline">
                                    Name: <input id="editedMonsterNameValue" class="form-control ml-1 mr-1" maxlength="26" type="text" value="{{ $monster->name }}">
                                    <button class="btn btn-success" style="cursor:pointer;" type="button" onclick="saveName({{ $monster->id }})">
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
                        <div class="col-4">
                            @if ($logged_in)
                                @if ($segment_name == 'head')
                                    <h5 id="monsterLevel" onclick="editLevel()" style="cursor:pointer;">
                                        Type: <i id="monsterLevelValue">{{ $monster->level }}</i>
                                        <small>
                                            <i class="fa fa-pen ml-1"></i>
                                        </small>
                                    </h5>
                                    <h5 id="editMonsterLevel" class="d-none text-nowrap form-inline">
                                        Type: <select id="editedMonsterLevelValue" maxlength="20" class="form-control ml-1 mr-1">
                                            <option value="basic" {{ ($monster->level == "Basic" ? "selected" : "" ) }}>Basic</option>
                                            <option value="standard" {{ ($monster->level == "Standard" ? "selected" : "" ) }}>Standard</option>
                                            @if ($user && $user->vip)
                                                <option value="pro" {{ ($monster->level == "Pro" ? "selected" : "" ) }}>Pro</option>
                                            @endif
                                        </select>
                                        <button class="btn btn-success" style="cursor:pointer;" type="button" onclick="saveLevel({{ $monster->id }})">
                                            <i class="fa fa-check"></i>
                                        </button>
                                    </h5>
                                    @if ($user->allow_nsfw)
                                        <h5>
                                            <div class="custom-control custom-switch mb-2">
                                                <input type="checkbox" onchange="saveNSFW({{ $monster->id }}, this)" {{ $monster->nsfw ? 'checked' : '' }} name="nsfw" class="custom-control-input" id="nsfw">
                                                <label class="custom-control-label" for="nsfw" {{ $user->allow_nsfw ? '' : 'disabled' }}>
                                                    NSFW
                                                </label>
                                            </div>
                                        </h5>
                                    @endif
                                @else 
                                    <h5 id="monsterLevel">
                                        <i id="monsterLevelValue">{{ $monster->level }}</i>
                                    </h5>
                                    @if ($monster->nsfw == 1)
                                        <h5 class="text text-danger">
                                            NSFW
                                        </h5>
                                    @endif
                                @endif
                            @endif
                        </div>
                        <div class="col-3">
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
                        :user="{{ $user ? $user : 'null' }}"
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
                alert('failure:' + err.message);
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

    function editLevel(){
        $('#monsterLevel').addClass('d-none');
        $('#editMonsterLevel').removeClass('d-none');
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
                'action' : 'updateName',
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

    function saveLevel(monster_id){
        var new_level = $('#editedMonsterLevelValue').val();

        $.ajax({
            url: '/updateLevel',
            method: 'POST',      
            data: { 
                'monster_id' : monster_id,
                'monster_level' : new_level,
                'action' : 'updateLevel',
                "_token": "{{ csrf_token() }}"
            },
            success: function(response){
                if (response == 'success'){
                    $('#monsterLevelValue').text($('#editedMonsterLevelValue option:selected').text());
                    $('#monsterLevel').removeClass('d-none');
                    $('#editMonsterLevel').addClass('d-none');
                }
            },
            error: function(err){
                alert('failed to save');
            }
        });
    }

    function saveNSFW(monster_id, el){
        var nsfw = $(el).is(':checked') ? 1 : 0;

        $.ajax({
            url: '/updateIsNSFW',
            method: 'POST',      
            data: { 
                'monster_id' : monster_id,
                'nsfw' : nsfw,
                'action' : 'updateIsNSFW',
                "_token": "{{ csrf_token() }}"
            },
            success: function(response){
                if (response == 'success'){

                }
            },
            error: function(err){
                alert('failed to save');
            }
        });
    }


</script>