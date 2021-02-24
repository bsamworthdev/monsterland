@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4>Hall Of Fame</h4>
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
                        path = "halloffame"
                        search = "{{ $search }}"
                        page-type = "hallOfFame"
                        is-my-page=0
                        group-id="{{ $group_id }}">

                    </top-rated-component>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
