@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"> 
                    <h4>Draw your monster's {{ $segment_name }}</h4>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <canvas-component
                        segment_name="{{ $segment_name }}"
                        monster="{{ $monster }}"
                    >
                    </canvas-component>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection