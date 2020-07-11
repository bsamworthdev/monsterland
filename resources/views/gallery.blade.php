@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header"> 
                    <div class="col-9">
                        Gallery
                    </div>
                    <div class="col-3">
                        <button class="btn btn-info" @click="backClick">Back</button>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <gallery-component
                        :monster="{{ $monster }}"
                        :prev-monster="{{ $prevMonster }}"
                        :next-monster="{{ $nextMonster }}"
                    >
                    </gallery-component>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection