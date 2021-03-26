@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    @if ($page_type == 'mymonsters')
                        <h4>My Monsters</h4>
                    @elseif ($page_type == 'myfavourites')
                        <h4>My Favourites</h4>
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
