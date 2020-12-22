<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Monsterland</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">


        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }

            html{
                background-color:transparent;
            }

            body{
                /* background-image:url('{{ asset('images/countryside_bg.jpg') }}'); */
                /*background-image:url('{{ asset('images/halloween_bg.jpg') }}'); */
                background-image:url('{{ asset('images/christmas_bg.png') }}');
                background-repeat: no-repeat;
                background-size: cover;
                background-attachment: fixed;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                /*position: relative;*/
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .content .text-body {
                padding:5px;
            }

            .title {
                font-size: 62px;
            }

            .links > a {
                color: #FFF;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
            .content{
                background-color:transparent;
                z-index:1;
                margin-top:60px;
            }
            .column {
                width:50%;
            }
            .column.left{
                text-align:right;
                padding-right:10px;
            }
            .column.right{
                text-align:left;
                padding-left:10px;
            }

            body, #bodyContainer{
                height: auto;
            }

            #monsterland_logo{
                width:100%;
            }
            .monsterland_featured{
                width:70%;
                max-width:300px;
                max-height:297px;
                margin-top:-1px;
                object-fit:cover;
                border:3px solid black;
            }
            .button_container{
                background-color:white;
                border-radius:20px;
                padding:20px 10px 9px 10px;
                max-width:700px;
                text-align:center;
                margin-left:auto;
                margin-right:auto;
                margin-top:20px;
                margin-bottom:10px;
                border:5px solid black;
            }
            .registerButton, .browseButton, .createButton, .viewButton{
                font-size:3vw;
            }
            .createButton, .viewButton{
                margin-bottom:10px;
            }
            .haveAccountButton{
                font-size:1.9vw;
            }
            .border {
                border-width:10px !important;
            }
            .gradient {
                background-image: linear-gradient(#9EE687, #E4FBDC);
            }
            .myShadow {
                box-shadow: -5px 5px #6a6a6a!important;
            }
            
            @media only screen and (min-width: 1024px) {
                #bodyContainer{
                    align-items: center;
                    display: flex;
                    justify-content: center;
                }
                .registerButton, .browseButton, .createButton, .viewButton{
                    font-size:24px;
                }
                .haveAccountButton{
                    font-size:20px;
                }
            }
        </style>
        <script type="text/javascript" src="https://platform-api.sharethis.com/js/sharethis.js#property=5f67b48601b108001af03582&product=image-share-buttons" async="async"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script>
            $(document).ready(function(){
                let searchParams = new URLSearchParams(window.location.search);
                if (searchParams.has('resetsession')){
                    $.ajax({
                        method: "POST",
                        url: "/resetsession",
                        data: {},
                        error: function(err){
                            alert(err);
                        }
                    })
                    .done(function() {
                        location.href="/";
                    });
                }
            })
        </script>
    </head>
    <body>
        <div id="bodyContainer" class="position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        <a href="{{ url('/home') }}">Home</a>
                    @else
                        <a href="/about">About</a>
                        <a href="{{ route('login') }}">Login</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}">Register</a>
                        @endif
                    @endauth
                </div>
            @endif

            <div class="content">
                <div class="title">
                    
                </div>
                <div class="text-body bg-secondary border border-dark rounded gradient">
                    <img id="monsterland_logo" class="navbar-brand" alt="monsterland" src="{{ asset('images/monsterland_logo_large.png') }}" />
                    <div class="break"></div>
                    <div class="button_container myShadow">
                        @guest
                            @if (Route::has('register'))
                            <div class='row'>
                                <div class='column left'>
                                        <button class="registerButton btn btn-success" onclick="location.href='{{ route('register') }}'">Create Account (It's free)</button>
                                        <br/>
                                        <a class="haveAccountButton" href="{{ route('login') }}">I already have an account</a>&nbsp;&nbsp;
                                </div>
                                <div class='column right'>
                                    <button class="browseButton btn btn-info pull-right" onclick="location.href='/nonauth/home'">Use Without Account</button>
                                </div>
                            </div>
                            @endif
                        @else
                            <div class='row'>
                                <div class='column left'>
                                    <button class="btn btn-success createButton" onclick="location.href='/home'">Create Monster</button>
                                </div>
                                <div class='column right'>
                                    <button class="btn btn-info viewButton" onclick="location.href='/gallery'">View Gallery</button>
                                </div>
                            </div>
                        @endguest
                    </div>
                    <h4 class="mt-5">Featured Monster: 
                        <a class="text-dark" href="/gallery/{{ $monster['id'] }}">
                            <b>{{ $monster['name'] }}</b>
                        </a>
                    </h4>
                    <h5>
                        <i>Created: {{ $monster['created_at_tidy'] }}</i>
                    </h5>
                    <a href="/gallery/{{ $monster['id'] }}">
                        <img class="monsterland_featured noshare myShadow rounded mb-5" src="/storage/{{ $monster['id'] }}.png" alt="{{ $monster['name'] }}">
                    </a>
                </div>

                <div class="links">
                   
                </div>
            </div>
        </div>
        <div class="text-center mt-3 mb-1">
            <a href="https://www.indiedb.com/games/monsterlandnet" title="View Monsterland.net on Indie DB" target="_blank"><img src="https://button.indiedb.com/rating/medium/games/74753.png" alt="Monsterland.net" /></a>
        </div>
    </body>
</html>
