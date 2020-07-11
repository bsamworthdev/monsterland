@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4>Lobby</h4>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <waiting-room-component
                        :monsters="{{ $monsters }}"
                        :user_id="{{ $user_id }}">
                    </waiting-room-component>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
