@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">

                <div class="card-header">
                    <h4>Preview Book</h4>
                </div>
                <div class="card-body">
                    <book-preview-component
                        :monsters ="{{ $monsters }}"
                        book-title = "{{ $bookTitle }}">
                    </book-preview-component>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection