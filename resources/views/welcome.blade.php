<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Monsterland</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <link href="{{ asset('css/style.css') }}" rel="stylesheet">

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

            /* body::after {
                content: "";
                background-image:url('{{ asset('images/christmas_bg3.jpg') }}');
                background-repeat: repeat;
                background-size: cover;
                background-attachment: fixed;
                opacity: 0.7;
                top: 0;
                left: 0;
                bottom: 0;
                right: 0;
                position: absolute;
                z-index: -1;   
            } */
            body {
                background-image:url('{{ asset('images/countryside_bg.jpg') }}');
                /*background-image:url('{{ asset('images/halloween_bg.jpg') }}'); */
                /* background-image:url('{{ asset('images/christmas_bg3.jpg') }}');  */
                background-size: cover;
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
            /* .column {
                width:50%;
            } 
            .column.left{
                text-align:right;
                padding-right:10px;
            }
            .column.right{
                text-align:left;
                padding-left:10px;
            }*/

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
                //if (window.navigator.standalone) $('#withoutAccountRow').hide();
                // if (inIframe()===true) $('#withoutAccountRow').hide();
                if (isIOS()===true) $('#withoutAccountRow').hide();
            })
            // function inIframe () {
            //     try {
            //         return window.self !== window.top;
            //     } catch (e) {
            //         return true;
            //     }
            // }
            function isIOS(){
                var inBrowser = typeof window !== 'undefined';
                var inWeex = typeof WXEnvironment !== 'undefined' && !!WXEnvironment.platform;
                var weexPlatform = inWeex && WXEnvironment.platform.toLowerCase();
                var UA = inBrowser && window.navigator.userAgent.toLowerCase();
                var safari = (UA && /safari/.test(UA)) || (weexPlatform === 'ios');
                var ios = (UA && /iphone|ipad|ipod|ios/.test(UA)) || (weexPlatform === 'ios');

                if(ios) {
                    if ( safari ) {
                        return false;
                    } else if ( !safari ) {
                        return true;
                    };
                } else {
                    return false;
                };
            }

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
                    <img id="monsterland_logo" class="navbar-brand noshare" alt="monsterland" src="{{ asset('images/monsterland_logo_large.png') }}" />
                    <div class="break"></div>
                    <div class="button_container myShadow">
                        @guest
                            @if (Route::has('register'))
                                <div class='row'>
                                    <div class='col-12 col-md-6 text-right mb-2'>
                                        <button class="registerButton btn btn-success btn-block" onclick="location.href='{{ route('login') }}'">Log in</button>
                                    </div>
                                    <div class='col-12 col-md-6 text-left mb-2'>
                                        <button class="registerButton btn btn-success btn-block" onclick="location.href='{{ route('register') }}'">Register (It's free)</button>
                                    </div>
                                </div>
                                @if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== "web2application.a471481609021114.com.myapplication")
                                    <div class='row' id="withoutAccountRow">  
                                        <div class='col-12 mb-2'>
                                            <button class="browseButton btn btn-info pull-right btn-block" onclick="location.href='/nonauth/home'">Use Without Account</button>
                                        </div>
                                    </div>
                                @endif
                                
                            @endif
                        @else
                            <div class='row'>
                                <div class='col-12 col-md-6 text-right'>
                                    <button class="btn btn-success createButton btn-block" onclick="location.href='/home'">Create Monster</button>
                                </div>
                                <div class='col-12 col-md-6 text-left'>
                                    <button class="btn btn-info viewButton text-dark btn-block" onclick="location.href='/gallery'">View Gallery</button>
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
