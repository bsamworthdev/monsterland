@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header text-nowrap">
                    @if ($is_my_page)
                        @if ($page_type=='favourites')
                            <h4>
                                My Favourites
                                <i class="fa fa-heart"></i>
                            </h4>
                        @else
                            <div class="container">
                                <div class="row">
                                    <div class="col-6 ml-0 pl-0">
                                        <h4>My Monsters</h4>
                                    </div>
                                    <div class="col-6 d-flex align-items-center justify-content-end">
                                        <div class="container">
                                            <div class="row">
                                                <div class="d-flex align-items-center justify-content-end col-12 col-lg-6">
                                                    <label class="statLabel mb-0">Following: </label>
                                                    <span class="pr-3 pl-1">
                                                        {{ $following_count }}
                                                    </span>
                                                </div>
                                                <div class="d-flex align-items-center justify-content-end col-12 col-lg-6" >
                                                    <label class="statLabel mb-0">Followers: </label>
                                                    <span class="pr-3 pl-1">
                                                        {{ $followers_count }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
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
                                        
                                        {{ $user->name }} 
                                        @if ($user->vip == 1)
                                            <i class="fa fa-star" title="VIP member"></i> 
                                        @endif
                                        @if ($page_type=='favourites')
                                            Favourites 
                                            <i class="fa fa-heart"></i>
                                        @endif
                                    </h4>
                                </div>
                                <div class="col-6">  
                                    @if ($page_type!=='favourites')    
                                       <follow-header-component
                                            :user="{{ $user }}"
                                            following="{{ $following }}"
                                            following-count="{{ $following_count }}"
                                            followers-count="{{ $followers_count }}"
                                            logged-in="{{ Auth::check() }}">
                                       </follow-header-component>
                                    @endif
                                </div>
                                    
                            </div>
                        </div>
                    @endif
                    @if ($page_type != 'favourites')
                        <user-stats-header-component
                            class="d-inline-block pl-0"
                            :stats="{{ $stats }}"
                            :trophies="{{ $user->trophies }}"
                            is-my-page="{{ $is_my_page }}"
                            :user="{{ $user }}">
                        </user-stats-header-component>
                    @endif
                </div>

                @if(Auth::check() && Auth::user()->id == 1 && $page_type != 'favourites')
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

                    <top-rated-component class="mt-4"
                        user="{{ $user }}"
                        :monsters="{{ $top_monsters }}"
                        :page = "{{ $page }}"
                        time-filter = "{{ $time_filter }}"
                        path = "{{ $page_type == 'favourites' ? 'favourites' : 'monsters' }}/{{$user->id}}"
                        search = "{{ $search }}"
                        page-type="{{ $page_type }}"
                        is-my-page="{{ $is_my_page }}">

                    </top-rated-component>

                    @if (Auth::check() && $page_type != 'favourites')
                        <user-stats-component
                            class="mt-5"
                            :current-user-id="{{ Auth::User()->id }}"
                            :user="{{ $user }}"
                            :stats="{{ $stats }}"
                            is-my-page="{{ $is_my_page }}">
                        </user-stats-component>
                    @endif
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
