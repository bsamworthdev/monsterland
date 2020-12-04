@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="login_list" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" href="#standard" role="tab" aria-controls="standard" aria-selected="true">{{ __('Login') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link"  href="#private_group" role="tab" aria-controls="private_group" aria-selected="false">Private Group</a>
                        </li>
                    </ul>
                </div>


                <div class="container">
                    <div class="row">
                        <div class="col-12 p-0">
                            <div class="card border-0">
                                <div class="card-body">

                                    <div class="tab-content mt-3">
                                        <div class="tab-pane active" id="standard" role="tabpanel">
                                            <form method="POST" action="{{ route('login') }}">
                                                @csrf

                                                <div class="form-group row">
                                                    <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                                                    <div class="col-md-6">
                                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                                        @error('email')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                                                    <div class="col-md-6">
                                                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                                        @error('password')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <div class="col-md-6 offset-md-4">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                                            <label class="form-check-label" for="remember">
                                                                Stay signed in for a week
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group row mb-0">
                                                    <div class="col-md-8 offset-md-4">
                                                        <button type="submit" class="btn btn-primary">
                                                            {{ __('Login') }}
                                                        </button>

                                                        @if (Route::has('password.request'))
                                                            <a class="btn btn-link" href="{{ route('password.request') }}">
                                                                {{ __('Forgot Your Password?') }}
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </form>
                                        </div>

                                        <div class="tab-pane" id="private_group" role="tabpanel" aria-labelledby="private_group-tab">  
                                            <form method="POST" action="nonauth/entergroup">

                                                @csrf
                                                <div class="form-group row">
                                                    <label for="name" class="col-md-4 col-form-label text-md-right">Your Name</label>

                                                    <div class="col-md-6">
                                                        <input id="name" type="text" class="form-control" name="name" value="" required autocomplete="name" autofocus>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="group_code" class="col-md-4 col-form-label text-md-right">Group Code</label>

                                                    <div class="col-md-6">
                                                        <input id="group_code" type="text" class="form-control" name="group_code" value="" required autocomplete="code" autofocus>

                                                        @if($errors->any())
                                                        <span class="invalid-feedback d-block" role="alert">
                                                            <strong>{{$errors->first()}}</strong>
                                                        </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="form-group row mb-0">
                                                    <div class="col-md-8 offset-md-4">
                                                        <button type="submit" class="btn btn-primary">
                                                            Enter
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).ready(function(){
        $('#login_list a').on('click', function (e) {
            localStorage.setItem('activeTab', $(e.target).attr('href')); 
            e.preventDefault()
            $(this).tab('show')
        })
        var activeTab = localStorage.getItem('activeTab');
        if(activeTab){
            $('#login_list a[href="' + activeTab + '"]').tab('show');
        }
    });
</script>
