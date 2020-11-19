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
                    <h4 class="d-inline-block">Monsters by {{ $user->name }} 
                        @if ($user->vip == 1)
                            <i class="fa fa-star" title="VIP member"></i> 
                        @elseif(Auth::user()->id == 1)
                            <button class="btn btn-success ml-2" onclick="gildUser({{ $user->id }})">
                                <i class="fa fa-star" title="VIP member"></i> 
                                Make VIP
                            </button>
                        @endif
                    </h4>
                    <trophies-header
                        :trophies="{{ $user->trophies }}">
                    </trophies-header>
                    @endif
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
