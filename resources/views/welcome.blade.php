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

            body{
                background-image:url('{{ asset('images/countryside_bg.jpg') }}'); 
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
                position: relative;
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
                padding:20px;
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
                background-color:white;
                z-index:1;
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
            
            @media only screen and (min-width: 1024px) {
                #bodyContainer{
                    align-items: center;
                    display: flex;
                    justify-content: center;
                }
            }
        </style>
    </head>
    <body>
        <div id="bodyContainer" class="position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        <a href="{{ url('/home') }}">Home</a>
                    @else
                        <a href="{{ route('login') }}">Login</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}">Register</a>
                        @endif
                    @endauth
                </div>
            @endif

            <div class="content">
                <div class="title">
                    Monsterland
                </div>
                <div class="text-body">
                    <h4>Welcome, weary traveller. </h4>
                    <p>Welcome to <b>MONSTERLAND</b>- the home of the oddest, quirkiest and downright strangest creatures you'll ever see.</p>
                    <p>Come in, have a look at some of our creations. And, maybe you could try your hand at making your own...if you're brave enough!!</p>
                    @guest
                        @if (Route::has('register'))
                        <div class='row'>
                            <div class='column left'>
                                    <button class="registerButton btn btn-success" onclick="location.href='{{ route('register') }}'">Create Account (It's free)</button>
                                    <br/>
                                    <a href="{{ route('login') }}">I already have an account</a>&nbsp;&nbsp;
                            </div>
                            <div class='column right'>
                                <button class="browseButton btn btn-info pull-right" onclick="location.href='/nonauth/home'">Use Without Account</button>
                            </div>
                          </div>
                        @endif
                    @else
                        <div class='row'>
                            <div class='column left'>
                                <button class="btn btn-success" onclick="location.href='/home'">Create Monster</button>
                            </div>
                            <div class='column right'>
                                <button class="btn btn-info" onclick="location.href='/gallery'">View Gallery</button>
                            </div>
                        </div>
                    @endguest
                    <br/>
                    <h5>A guide to monsterland...</h5>
                    <div style="position:relative;padding-top:56.25%;">
                        <iframe style="position:absolute;top:0;left:0;" width="100%" height="100%" src="https://www.youtube.com/embed/wAvBrfjaROU" frameborder="1" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                </div>

                <div class="links">
                   
                </div>
            </div>
        </div>
    </body>
</html>
