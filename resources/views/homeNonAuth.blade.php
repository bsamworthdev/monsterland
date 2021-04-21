@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="{{ $group_mode ? 'col-xl-10':'col-xl-8' }} col-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h4>Lobby</h4>
                    @if ($group_mode ?? false)
                        <h5>Group: {{ $group_name ?? '' }}</h5>
                        <h5>User: {{ $group_username ?? '' }}</h5>
                    @endif
                </div>

                @if (!$group_mode ?? false)
                    @foreach ($info_messages as $message)
                        <div class="row justify-content-center mt-1 ml-2 mr-2">
                            <div class="col-12">
                                <div class="alert alert-{{ $message->style }}">
                                    {!! $message->text !!}
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if (\Session::has('error'))
                        <div class="alert alert-danger">
                            {!! \Session::get('error') !!}
                        </div>
                    @endif

                    {{-- @if ($group_mode) --}}
                    <waiting-room-non-auth-component
                        :monsters="{{ $unfinished_monsters }}"
                        session_id="{{ $session_id }}"
                        :daily-action-count = "{{ $daily_action_count }}">
                    </waiting-room-non-auth-component>
                    {{-- @endif --}}
                </div>
            </div>
        </div>
        @if (!$group_mode)
        <div class="col-xl-4 col-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h4>Mini-games</h4>
                </div>
                <div class="card-body">
                    <a class="btn btn-info btn-block" href="/taggame">
                        Play Monster Tagging Game
                    </a>
                    <a class="btn btn-info btn-block" href="https://www.etsy.com/uk/shop/MonsterlandStore">
                        Visit Monsterland Shop <i class="fas fa-external-link-alt"></i>
                    </a>
                    <a class="btn btn-info btn-block" href="/tshirtguide">
                        Get your monster on a T-shirt
                    </a>
                </div>
            </div>
            <div class="card mb-3">

                <div class="card-header">
                    <h4>Stats</h4>
                </div>
                <div class="card-body">
                <overall-stats-component
                    :overall-stats = "{{ $overall_stats }}"
                >
                </overall-stats-component>
                </div>
            </div>
            @if (1=2)
            <div class="card mb-3">
                <div class="card-header">
                    <h4>Live Chat</h4>
                </div>
                <div class="card-body" style="min-height:200px;">
                    <iframe src="https://titanembeds.com/embed/828001307368357909?theme=IceWyvern&username=GUEST" id="liveChat" style="display:none;width:100%; min-height:400px;" frameborder="0"></iframe>
                    <div id="liveChatSpinner" class="spinner-border mt-5" style="left: 50%; margin-left: -1em; position:relative;" role="status">
                        <span class="sr-only"> Loading...</span>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif;
    </div>
</div>
@endsection
<script>
   window.onload = function(){
       showChat();
   };

    function showChat(){
        var iframe = $('iframe');
        if (iframe.find('#chatcontent')){  
            $('#liveChat').css('display','block');
            $('#liveChatSpinner').css('display','none');
        } else {
            setTimeout(function(){
                showChat();
            },1000);
        }
    }
</script>
