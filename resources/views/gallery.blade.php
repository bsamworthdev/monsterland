@extends('layouts.app')
@section('image_url', 'https://monsterland.net/storage/'.$monster->id.'.png')

@section('content')
<div class="container">
    @if (\Session::has('error'))
        <div class="alert alert-danger">
            {!! \Session::get('error') !!}
        </div>
    @endif
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header"> 
                    <div class="row">
                        <div class="col-6">
                            <h4>{{ $title }}</h4>
                        </div>
                        <div class="col-6">
                            <button id="btnBack" class="btn btn-info btn-block" style="display:none" onclick="backClick()">
                            &nbsp;
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if (is_null($user))
                        <gallery-component
                            :monster="{{ $monster }}"
                            :prev-monster="{{ $prevMonster }}"
                            :next-monster="{{ $nextMonster }}"
                            :group-mode="{{ $groupMode }}"
                            :everyone-can-use-store="{{ $everyoneCanUseStore }}"
                        >
                        </gallery-component>
                    @else
                        <gallery-component
                            :monster="{{ $monster }}"
                            :user="{{ $user }}"
                            :prev-monster="{{ $prevMonster }}"
                            :next-monster="{{ $nextMonster }}"
                            :group-mode="{{ $groupMode }}"
                            :everyone-can-use-store="{{ $everyoneCanUseStore }}"
                        >
                        </gallery-component>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
<script>

    var referrer;

    window.onload=(function(){
        var queryString = window.location.search;
        var urlParams = new URLSearchParams(queryString);
        var pageType = '';
        referrer = urlParams.get('ref');

        if (referrer == 'gallery'){
            pageType="Gallery";
        } else if (referrer == 'halloffame'){
            pageType="Hall Of Fame";
        } else if (referrer == 'mymonsters'){
            pageType="My Monsters";
        } else if (referrer == 'favourites'){
            pageType="Favourites";
        } else if (referrer && referrer.split('_')[0] == 'usermonsters'){
            pageType="Monsters";
        } else {
            pageType="Lobby";
        }
        document.getElementById('btnBack').style.display='block';
        document.getElementById('btnBack').innerText = 'Back to ' + pageType;
    })

    function backClick(){
        if (referrer == 'gallery'){
            location.href="/monstergrid";
        } else if (referrer == 'halloffame'){
            location.href="/monstergrid/halloffame";
        } else if (referrer == 'mymonsters'){
            location.href="/monstergrid/mymonsters";
        } else if (referrer == 'favourites'){
            location.href="/monstergrid/favourites";
        } else if (referrer && referrer.split('_')[0] == 'usermonsters'){
            location.href="/monstergrid/usermonsters/" + referrer.split('_')[1];
        } else {
            location.href="/nonauth/home";
        }
    }
</script>