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
                                    @if ($my_page)
                                        <h4>My Monsters</h4>
                                    @else 
                                        <h4>{{ $user->name }}</h4>
                                    @endif
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
                        @elseif ($page_type == 'myfavourites')
                            <h4>My Favourites <i class="fa fa-heart"></i></h4>
                        @elseif ($page_type == 'halloffame')
                            <h4>Hall Of Fame</h4>
                        @else 
                            <h4>Gallery</h4>
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

                    <gallery-new-component class="mt-4"
                        user="{{ $user }}"
                        group-id="{{ $group_id }}"
                        page-type="{{ $page_type }}">

                    </gallery-new-component>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
