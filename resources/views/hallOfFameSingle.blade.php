@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header"> 
                    <div class="row">
                        <div class="col-6">
                            <h4>Hall Of Fame</h4>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if (is_null($user))
                        <top-rated-single-component
                            :monster="{{ $monster }}"
                            :monster-count="{{ $monsterCount }}"
                            :group-mode="{{ $groupMode }}"
                            page-type="{{ $pageType ?? '' }}"
                            time-filter = "{{ $time_filter }}"
                            search = "{{ $search }}"
                            :skip = {{ $skip }}>
                        </top-rated-single-component>
                    @else
                        <top-rated-single-component
                            :monster="{{ $monster }}"
                            :monster-count="{{ $monsterCount }}"
                            :user="{{ $user }}"
                            :group-mode="{{ $groupMode }}"
                            page-type="{{ $pageType ?? '' }}"
                            time-filter = "{{ $time_filter }}"
                            search = "{{ $search }}"
                            :skip = {{ $skip }}>
                        </top-rated-single-component>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
<script>
    function backClick(){
        location.href="/home";
    }
</script>