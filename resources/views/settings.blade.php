@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">

                <div class="card-header">
                    <h4>Settings</h4>
                </div>
                <div class="card-body">
                    <settings-component
                        :user-id="{{ $user_id }}"
                        :is-patron="{{ $is_patron }}"
                        :allow-monster-emails="{{ $allow_monster_emails }}"
                        :allow-nsfw = "{{ $allow_NSFW }}"
                        :peek-view-activated = "{{ $peek_view_activated }}"
                        :follower-notify = "{{ $follower_notify }}"
                        :redis-activated = "{{ $redis_activated }}">
                    </settings-component>   
                </div>
            </div>
        </div>
    </div>
</div>
@endsection