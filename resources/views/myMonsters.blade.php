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
                    <h4>Monsters by {{ $user->name }} 
                        @if ($user->vip == 1)
                            <i class="fa fa-star" title="VIP member"></i> 
                        @elseif(Auth::user()->id == 1)
                            <button class="btn btn-success ml-2" onclick="gildUser({{ $user->id }})">
                                <i class="fa fa-star" title="VIP member"></i> 
                                Make VIP
                            </button>
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
                        :current_user_id="{{ Auth::User()->id }}"
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
</script>
