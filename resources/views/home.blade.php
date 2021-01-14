@extends('layouts.app')

@section('content')
<div class="container">
    @if (\Session::has('error'))
        <div class="alert alert-danger">
            {!! \Session::get('error') !!}
        </div>
    @endif
    <div class="row justify-content-center">
        <div class="col-xl-8 col-12 mb-4">
            <div class="card">

                <div class="card-header">
                    <h4>Lobby</h4>
                </div>
                
                @foreach ($info_messages as $message)
                    <div class="row justify-content-center mt-1 ml-2 mr-2 info-message" message_id="{{ $message->id }}">
                        <div class="col-12">
                            <div class="alert alert-{{ $message->style }} mb-0">
                                <div class="row">
                                    <div class="col-10 col-xl-11">
                                    {!! $message->text !!}
                                    </div>
                                    <div class="col-2 col-xl-1 text-right">
                                        <i onclick="closeMessage(this)" class="fa fa-times" title="Delete"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    
                    <waiting-room-component
                        :flagged-monsters="{{ $flagged_monsters }}"
                        :flagged-comment-monsters="{{ $flagged_comment_monsters }}"
                        :monitored-monsters="{{ $monitored_monsters }}"
                        :take-two-monsters="{{ $take_two_monsters }}"
                        :monsters="{{ $unfinished_monsters }}"
                        :user_id="{{ $user_id }}"
                        :user_is_vip={{ $user_is_vip }}
                        :user_allows_nsfw={{ $user_allows_nsfw }}
                        :random-words = "{{ $random_words }}">
                    </waiting-room-component>

                </div>
            </div>
        </div>
        <div class="col-xl-4 col-12 p-0">
            <div class="col-12 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h4>Live Feed</h4>
                    </div>
                    <div class="card-body">
                        <user-changes-component
                            :user_id="{{ $user_id }}"
                            :changes="{{ $audit_actions }}"
                        >
                        </user-changes-component>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card mb-3">

                    <div class="card-header">
                        <h4>Random Monster</h4>
                    </div>
                    <div class="card-body">
                        <random-monster-component
                            :monster="{{ $random_monster }}"
                        >
                        </random-monster-component>
                    
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">

                    <div class="card-header">
                        <h4>Weekly Leaderboard</h4>
                    </div>
                    <div class="card-body">
                        <leader-board-component
                            :user_id="{{ $user_id }}"
                            :stats="{{ $leader_board_stats }}"
                        >
                        </leader-board-component>
                    
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] !== "web2application.a471481609021114.com.myapplication")
        <input id="androidKey" type="hidden" value="f349{{$user_id}}v4t3">
    @endif
    <input id="iosKey" type="hidden" value="q34s{{$user_id}}v41U">
</div>
@endsection
<script>
    window.onload= (function(){
        var androidKey = $('#androidKey').val();
        var iosKey = isIOS() ? $('#iosKey').val() : '';
        var key = androidKey || iosKey;

        if (key){
            $.ajax({
                method: "POST",
                url: "/setHasUsedApp",
                data: {
                    key: key,
                    action: 'setHasUsedApp'
                }
            })
            .done(function() {
                $('#iosKey').remove();
            });
        }
    });

    function closeMessage(el){
        var $msg = $(el).closest('.info-message');
        $.ajax({
            method: "POST",
            url: "/closeInfoMessage",
            data: { 
                message_id: $msg.attr('message_id'), 
                action: 'closeInfoMessage'
            }
        })
        .done(function() {
            $msg.hide();
        });
    }

    function isIOS(){
        var inBrowser = typeof window !== 'undefined';
        var inWeex = typeof WXEnvironment !== 'undefined' && !!WXEnvironment.platform;
        var weexPlatform = inWeex && WXEnvironment.platform.toLowerCase();
        var UA = inBrowser && window.navigator.userAgent.toLowerCase();
        var safari = (UA && /safari/.test(UA)) || (weexPlatform === 'ios');
        var ios = (UA && /iphone|ipad|ipod|ios/.test(UA)) || (weexPlatform === 'ios');

        if(ios) {
            if ( safari ) {
                return false;
            } else if ( !safari ) {
                return true;
            };
        } else {
            return false;
        };
    }
</script>
