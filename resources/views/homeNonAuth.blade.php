@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4>Lobby</h4>
                    @if ($group_mode ?? false)
                        <h5>Group: {{ $group_name ?? '' }}</h5>
                        <h5>User: {{ $group_username ?? '' }}</h5>
                    @endif
                </div>

                @if (!$group_mode ?? false)
                    @foreach ($info_messages as $message)
                        <div class="row justify-content-center mt-1 ml-2 mr-2">
                            <div class="col-12">
                                <div class="alert alert-{{ $message->style }}">
                                    {!! $message->text !!}
                                </div>
                            </div>
                        </div>
                    @endforeach
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

                    <waiting-room-non-auth-component
                        :monsters="{{ $unfinished_monsters }}"
                        session_id="{{ $session_id }}">
                    </waiting-room-non-auth-component>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
