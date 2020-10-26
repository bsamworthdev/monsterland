@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">

                <div class="card-header">
                    <div class="row">
                        <div class="col-6">
                            <h4 class="pull-left">Preview Book</h4>
                        </div>
                        <div class="col-3">
                            <button class="btn btn-info pull-right btn-block" 
                                onclick="location.href='/book/build/{{ $book->group_id }}/{{ $book->id }}';">
                                Back
                            </button>
                        </div>
                        <div class="col-3">
                            <button class="btn btn-success pull-right btn-block">Continue</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <book-preview-component
                        :monsters ="{{ $monsters }}"
                        book-title = "{{ $book->title }}">
                    </book-preview-component>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection