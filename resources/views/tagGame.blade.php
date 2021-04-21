@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">

                <div class="card-header">
                    <h4>Monster Tagging Game</h4>
                </div>
                <div class="card-body">
                   <tag-game-component
                        user-name = "{{ $user_name }}"
                        :monsters = "{{ $monsters }}"
                        :top-scores = "{{ $top_scores }}"
                        :logged-in = "{{ $logged_in }}"
                        :is-patron = "{{ $is_patron }}"
                        :has-used-app = "{{ $has_used_app }}"
                        >
                   </tag-game-component>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection