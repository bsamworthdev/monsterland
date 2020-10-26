@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">

                <div class="card-header">
                    <h4>Create Book</h4>
                </div>
                <div class="card-body">
                   <group-image-selector-component
                    :group-id="{{ $group_id }}"
                    :monsters="{{ $monsters }}"
                    :book-monsters="{{ $book_monsters }}">
                   </group-image-selector-component>   
                </div>
            </div>
        </div>
    </div>
</div>
@endsection