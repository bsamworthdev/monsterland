@extends('layouts.app')

@section('head')
<script type="text/javascript" src="https://platform-api.sharethis.com/js/sharethis.js#property=5f67b48601b108001af03582&product=image-share-buttons" async="async"></script>
<meta property="og:title" content="Look at this monster I created on monsterland.net" />
<meta property="og:url" content="https://monsterland.net" />
<meta property="og:description" content="Monsterland is an online drawing game, where you collaborate with other 
artists to create wonderful or hideous monsters. It's free and has no ads. You don't even need an account. 
Why not come and have a go!" />
<meta property="og:site_name" content="Monsterland" />
<meta property="og:image" content="https://monsterland.net/storage/{{ $monster->id }}.jpg" />
@stop

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header"> 
                    <div class="row">
                        <div class="col-6">
                            <h4>Gallery</h4>
                        </div>
                        <div class="col-6">
                            @if (!is_null($user))
                                <button class="btn btn-info btn-block" onclick="backClick()">Return to lobby</button>
                            @endif
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
                        >
                        </gallery-component>
                    @else
                        <gallery-component
                            :monster="{{ $monster }}"
                            :user="{{ $user }}"
                            :prev-monster="{{ $prevMonster }}"
                            :next-monster="{{ $nextMonster }}"
                            :group-mode="{{ $groupMode }}"
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
    function backClick(){
        location.href="/home";
    }
</script>