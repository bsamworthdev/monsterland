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
            <div class="card mb-3">

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
                        group-name="{{ $group_name }}"
                        :flagged-monsters="{{ $flagged_monsters }}"
                        :flagged-comment-monsters="{{ $flagged_comment_monsters }}"
                        :monitored-monsters="{{ $monitored_monsters }}"
                        :take-two-monsters="{{ $take_two_monsters }}"
                        :monsters="{{ $unfinished_monsters }}"
                        :user_id="{{ $user_id }}"
                        :user_is_vip={{ $user_is_vip }}
                        :user_allows_nsfw={{ $user_allows_nsfw }}
                        :random-words = "{{ $random_words }}"
                        :daily-action-count = "{{ $daily_action_count }}">
                    </waiting-room-component>

                </div>
            </div>
            @if ($group_name=='')
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
        @if ($group_name=='')
        <div class="col-xl-4 col-12 p-0">
            <div class="col-12 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h4>Links</h4>
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
            </div>
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
                <div class="card mb-3">

                    <div class="card-header">
                        <h4>Suggested Tags</h4>
                    </div>
                    <div class="card-body">
                        <suggested-tags-component
                            :tags="{{ $suggested_tags }}"
                        >
                        </suggested-tags-component>
                    
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card mb-3">

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
            <div class="col-12">
                <div class="card">

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
            </div>
        </div>
        @endif
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

        showChat();
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
