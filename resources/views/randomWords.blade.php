@extends('layouts.app')

@section('content')
<div class="container">
    @if(session()->has('message'))
        <div class="alert alert-success">
            {{ session()->get('message') }}
        </div>
    @endif
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4>Random Words</h4>
                </div>
                <div class="card-body">
                    <random-words-component
                        :random-words="{{ $random_words }}">
                    </random-words-component>  
                </div>
            </div>
        </div>
    </div>
</div>
@endsection