@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    @if ($page_type == 'mymonsters' || $page_type == 'usermonsters')
                        <div class="container">
                            <div class="row">
                                <div class="col-6 ml-0 pl-0">
                                    <h4 style="white-space:normal" >
                                        @if(Auth::check() && Auth::user()->allow_nsfw)
                                            @if ($user && $user->profilePic)
                                                <a href="/gallery/{{ $user->profilePic->monster_id }}">
                                                    <img class="profilePic border rounded" src="/storage/{{ $user->profilePic->monster_id }}_thumb.png">
                                                </a>
                                            @endif
                                        @else
                                            @if ($user && $user->profilePic && $user->profilePic->monster->nsfw == 0)
                                                <a href="/gallery/{{ $user->profilePic->monster_id }}">
                                                    <img class="profilePic border rounded" src="/storage/{{ $user->profilePic->monster_id }}_thumb.png">
                                                </a>
                                            @endif
                                        @endif
                                        
                                        @if ($my_page)
                                            My Monsters
                                        @else 
                                            {{ $user->name }} 
                                        @endif
                                        
                                        @if ($user->vip == 1)
                                            <i class="fa fa-star" title="VIP member"></i> 
                                        @endif
                                    </h4>

                                </div>

                                <div class="col-6">  
                                    <follow-header-component
                                        :user="{{ $user }}"
                                        following="{{ $following }}"
                                        following-count="{{ $following_count }}"
                                        followers-count="{{ $followers_count }}"
                                        logged-in="{{ Auth::check() }}"
                                        my-page="{{ $my_page }}">
                                    </follow-header-component>
                                    <social-media-component
                                        :social-media-accounts="{{ $user->socialMediaAccounts }}"
                                        :is-my-page = {{ $my_page }}>
                                    </social-media-component>
                                </div>
                            </div>
                        </div>
                        <user-stats-header-component
                            class="d-inline-block pl-0"
                            :stats="{{ $stats }}"
                            :trophies="{{ $user->trophies }}"
                            is-my-page="1"
                            :user="{{ $user }}">
                        </user-stats-header-component>
                    @elseif ($page_type == 'favourites')
                        <h4>My Favourites <i class="fa fa-heart"></i></h4>
                    @elseif ($page_type == 'halloffame')
                        <h4>Hall Of Fame</h4>
                    @else 
                        <h4>Gallery</h4>
                    @endif
                    @if ($group_name != '')
                    <h4>
                        Group: {{ $group_name }}
                    </h4>
                    @endif
                </div>

                <div class="card-body">
                    @if(Auth::check() && Auth::user()->id == 1 && $page_type == 'usermonsters')
                        <div class="mt-2">
                            @if ($user->vip)
                                <button class="btn btn-danger ml-2" onclick="ungildUser({{ $user->id }})">
                                    <i class="fa fa-star" title="VIP member"></i> 
                                    Remove VIP
                                </button>
                            @else
                                <button class="btn btn-success ml-2" onclick="gildUser({{ $user->id }})">
                                    <i class="fa fa-star" title="VIP member"></i> 
                                    Make VIP
                                </button>
                            @endif
                            @if ($user->needs_monitoring)
                                <button class="btn btn-danger ml-2" onclick="unmonitorUser({{ $user->id }})">
                                    <i class="fa fa-flag" title="VIP member"></i> 
                                    Remove "Dodgy drawer" flag
                                </button>
                            @else
                                <button class="btn btn-info ml-2" onclick="monitorUser({{ $user->id }})">
                                    <i class="fa fa-flag" title="VIP member"></i> 
                                    Add "Dodgy drawer" flag
                                </button>
                            @endif
                        </div>
                    @endif

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

                    @if (Auth::check() && ($page_type == 'mymonsters' || (Auth::User()->id == 1  && $page_type =='usermonsters')))
                        <user-stats-component
                            :user="{{ $user }}"
                            :stats="{{ $stats }}">
                        </user-stats-component>
                    @endif

                    <monster-grid-component class="mt-2"
                        user="{{ $user }}"
                        group-id="{{ $group_id }}"
                        page-type="{{ $page_type }}"
                        filters="{{ $filters }}">

                    </monster-grid-component>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
    function gildUser(user_id){
        $.ajax({
            method: "POST",
            url: "/monsters/gildUser",
            data: { 
                user_id: user_id, 
                action: 'gildUser'
            }
        })
        .done(function() {
            location.reload();
        });
    }
    function ungildUser(user_id){
        $.ajax({
            method: "POST",
            url: "/monsters/ungildUser",
            data: { 
                user_id: user_id, 
                action: 'ungildUser'
            }
        })
        .done(function() {
            location.reload();
        });
    }
    function monitorUser(user_id){
        $.ajax({
            method: "POST",
            url: "/monsters/monitorUser",
            data: { 
                user_id: user_id, 
                action: 'monitorUser'
            }
        })
        .done(function() {
            location.reload();
        });
    }
    function unmonitorUser(user_id){
        $.ajax({
            method: "POST",
            url: "/monsters/unmonitorUser",
            data: { 
                user_id: user_id, 
                action: 'unmonitorUser'
            }
        })
        .done(function() {
            location.reload();
        });
    }
</script>
