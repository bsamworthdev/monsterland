@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">

                <div class="card-header">
                    <h4>Lobby</h4>
                </div>

                @foreach ($info_messages as $message)
                    <div class="row justify-content-center mt-1 ml-2 mr-2 info-message" message_id="{{ $message->id }}">
                        <div class="col-12">
                            <div class="alert alert-{{ $message->style }} mb-0">
                                <div class="row">
                                    <div class="col-10 col-xl-11">
                                    {!! $message->text !!}
                                    </div>
                                    <div class="col-2 col-xl-1 text-right">
                                        <i onclick="closeMessage(this)" class="fa fa-times" title="Delete"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

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
                    
                    <waiting-room-component
                        :monsters="{{ $unfinished_monsters }}"
                        :user_id="{{ $user_id }}"
                        :user_is_vip={{ $user_is_vip }}>
                    </waiting-room-component>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
<script>
    function closeMessage(el){
        var $msg = $(el).closest('.info-message');
        $.ajax({
            method: "POST",
            url: "/closeInfoMessage",
            data: { 
                message_id: $msg.attr('message_id'), 
                action: 'closeInfoMessage'
            }
        })
        .done(function() {
            $msg.hide();
        });
    }
</script>
