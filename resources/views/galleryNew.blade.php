@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    @if ($page_type == 'mymonsters')
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
