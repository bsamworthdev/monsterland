@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header text-nowrap">
                    @if ($is_my_page)
                        <h4>My Monsters</h4>
                    @else
                        <h4 style="white-space:normal">Monsters by {{ $user->name }} 
                            @if ($user->vip == 1)
                                <i class="fa fa-star" title="VIP member"></i> 
                            @endif
                        </h4> 
                    @endif
                    <user-stats-header-component
                        class="d-inline-block pl-0"
                        :stats="{{ $stats }}"
                        :trophies="{{ $user->trophies }}"
                        is-my-page="{{ $is_my_page }}">
                    </user-stats-header-component>
                </div>

                @if(Auth::user()->id == 1)
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
                        path = "monsters/{{$user->id}}"
                        search = "{{ $search }}"
                        page-type="gallery"
                        is-my-page="{{ $is_my_page }}">

                    </top-rated-component>

                    <user-stats-component
                        class="mt-5"
                        :current-user-id="{{ Auth::User()->id }}"
                        :user="{{ $user }}"
                        :stats="{{ $stats }}"
                        is-my-page="{{ $is_my_page }}">
                    </user-stats-component>
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
