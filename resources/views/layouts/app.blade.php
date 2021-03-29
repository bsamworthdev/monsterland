<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="monsterland,exquisite corpse,drawing,game">
    <meta name="description" content="Monsterland- an exquisite corpse game">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Sharethis -->
    <meta property="og:title" content="Look at this monster I created on monsterland.net" />
    <meta property="og:url" content="https://monsterland.net" />
    <meta property="og:description" content="Monsterland is an online drawing game, where you collaborate with other 
    artists to create wonderful or hideous monsters. It's free and has no ads. You don't even need an account. 
    Why not come and have a go!" />
    <meta property="og:site_name" content="Monsterland" />
    <meta property="og:image" content="@yield('image_url')" />

    <title>{{ config('app.name', 'Laravel') }}- an exquisite corpse game</title>

    <!-- Scripts -->
    <script type="text/javascript" src="https://platform-api.sharethis.com/js/sharethis.js#property=5f67b48601b108001af03582&product=image-share-buttons" async="async"></script>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    @include('cookieConsent::index')
    <style>
        #includeNSFW{
            margin-left:0!important;
            
        }
        .nav-item.settings{
            margin-right:40px;
        }
        .navbar-brand{
            margin-right:1rem;
        }
        #monsterland_logo{
            width:103px;
        }
        .footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 30px;
            line-height: 30px;
            background-color: #FFFFFF;
        }
        html {
            position: relative;
            min-height: 100%;
        }

        body {
            margin-bottom: 49px;
        }
        .logo{
            width:20px;
        }
        .logos a{
            text-decoration:none!important;
        }
        .notificationsIcon{
            width:34px;
        }
        /* body::after {
            content: "";
            background-image:url('{{ asset('images/countryside_bg.jpg') }}'); 
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
            /* background-image:url('{{ asset('images/valentines_bg.jpg') }}'); */
            /* background-image:url('{{ asset('images/halloween_bg.jpg') }}'); */
            /* background-image:url('{{ asset('images/christmas_bg3.jpg') }}');  */
            background-size: cover;
            background-attachment: fixed;
        }

        .profilePic{
            height:50px;
            width:50px;
        }

        @media screen and (min-width: 1200px) {
            #monsterland_logo{
                width:193px;
            }
        }
        @media screen and (min-width: 992px) {
            .mobile-only{
                display:none!important;
            }
        }
        @media screen and (min-width: 375px) and (max-width: 991px) {
            .desktop-only{
                display:none!important;
            }
            .notificationsIcon{
                right:34px;
                position:absolute;
            }
            #notificationsButton{
                min-width:unset!important;
            }
            .notificationsIcon{
                min-height:50px;
            }
        }
        @media screen and (max-width: 374px) {
            .desktop-only{
                display:none!important;
            }
            #monsterland_logo{
                width:103px;
            }
        }
    </style>
    <script>

        function includeNSFW_clicked(e){

            var setTo = ($('#includeNSFW').is(':checked') ? 1 : 0);
            $(this).prop('disabled',true);

            $.ajax({
                method: "POST",
                url: "/updateNSFW",
                data: { checked: setTo, action: 'nsfw_setting' }
            })
            .done(function() {
                location.reload();
            });
        }
    </script>

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-W5CT252');</script>
    <!-- End Google Tag Manager -->

</head>
<body style="">  
    <div id="app">   
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
            <div class="container no-wrap">
                <a href="{{ url('/home') }}">
                    <img id="monsterland_logo" class="navbar-brand noshare" alt="monsterland" src="{{ asset('images/monsterland_logo.png') }}" />
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                @guest
                @else
                    <trophies-header
                        :trophies="{{ Auth::user()->trophies }}">
                    </trophies-header>
                    <notifications-header
                        class="notificationsIcon mobile-only float-right"
                        :user = "{{ Auth::user() }}"
                        :notifications="{{ Auth::user()->myLatestNotifications }}">
                    </notifications-header>
                @endguest

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">
                        @guest
                        <li class="nav-item">
                            <a class="nav-link" href="/nonauth/home">Lobby</a>
                        </li>
                        @else
                        <li class="nav-item">
                            <a class="nav-link" href="/home">Lobby</a>
                        </li>
                        @endguest
                        <li class="nav-item">
                            <a class="nav-link" href="/monstergrid">Gallery</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/monstergrid/halloffame">Hall Of Fame</a>
                        </li>
                        @guest
                        @else
                        <li class="nav-item">
                            <a class="nav-link" href="/privategroups">Private Groups</a>
                        </li>
                        @endguest
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if ($group_mode ?? false)
                                <li class="nav-item">
                                    <a class="nav-link" href="/?resetsession=true">Exit</a>
                                </li>
                            @else
                                <li class="nav-item">
                                    <a class="nav-link" href="/about">About</a>
                                </li>
                                @if (Auth::user() && Auth::user()->id == 1)
                                    <li class="nav-item">
                                        <a class="nav-link" href="/randomwords">Random Words</a>
                                    </li>
                                @endif
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                                @if (Route::has('register'))
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                    </li>
                                @endif
                            @endif
                        @else
                            <li class="nav-item desktop-only">
                                <notifications-header
                                    :user = "{{ Auth::user() }}"
                                    :notifications="{{ Auth::user()->myLatestNotifications }}">
                                </notifications-header>
                            </li>
                           
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle no-wrap" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    @if (Auth::user() && Auth::user()->profilePic && Auth::user()->profilePic->monster_id)
                                        <img class="profilePic border rounded mr-2" src="/storage/{{ Auth::user()->profilePic->monster_id }}_thumb.png">
                                    @endif
                                    @if (Auth::user()->vip == 1)
                                        <i class="fa fa-star" title="VIP member"></i> 
                                    @endif
                                
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="/about">About</a>
                                    <a class="dropdown-item" href="/monstergrid/mymonsters">My Monsters</a>
                                    <a class="dropdown-item" href="/monstergrid/favourites">
                                        My Favourites <i class="fa fa-heart pl-1"></i>
                                    </a>
                                    @if (Auth::user() && Auth::user()->id == 1)
                                        <a class="dropdown-item" href="/randomwords">Random Words</a>
                                    @endif
                                    <a class="dropdown-item" href="/settings">Settings</a>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>

        <div class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center">
                        <div class="d-none d-sm-inline pr-3">
                            Monsterland was created by 
                            <a target="_blank" href="http://bensamworthdevelopment.co.uk">Ben Samworth Development Ltd</a>
                        </div>
                        <div class="d-inline logos">
                            <a target="_blank" href="https://www.facebook.com/groups/2361369074167661">
                                <img class="logo" src="{{ asset('images/facebook.png') }}">
                            </a> |
                            <a target="_blank" href="https://twitter.com/SamworthBen">
                                <img class="logo" src="{{ asset('images/twitter.png') }}"> 
                            </a> |
                            <a target="_blank" href="https://www.reddit.com/r/monsterlandgame/">
                                <img class="logo" src="{{ asset('images/reddit.png') }}">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-W5CT252"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
</html>