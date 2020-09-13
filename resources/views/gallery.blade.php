@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header"> 
                    <div class="row">
                        <div class="col-6">
                            <h4>Gallery</h4>
                        </div>
                        <div class="col-6">
                            @if (!is_null($user))
                                <button class="btn btn-info btn-block" onclick="backClick()">Return to lobby</button>
                            @endif
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
                        <gallery-component
                            :monster="{{ $monster }}"
                            :prev-monster="{{ $prevMonster }}"
                            :next-monster="{{ $nextMonster }}"
                            :group-mode="{{ $groupMode }}"
                        >
                        </gallery-component>
                    @else
                        <gallery-component
                            :monster="{{ $monster }}"
                            :user="{{ $user }}"
                            :prev-monster="{{ $prevMonster }}"
                            :next-monster="{{ $nextMonster }}"
                            :group-mode="{{ $groupMode }}"
                        >
                        </gallery-component>
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