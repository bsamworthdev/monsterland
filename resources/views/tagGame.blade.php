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
                        :monsters = "{{ $monsters }}">
                   </tag-game-component>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection