<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <style>
        #app{
            min-width:900px;
        }
        .pageWarning{
            display:none;;
        }
        #monsterland_logo{
            width:103px;
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
            z-index:-1;   
            width:200%;
            height:200%;
        } */
        body {
            background-image:url('{{ asset('images/countryside_bg.jpg') }}');
            /* background-image:url('{{ asset('images/valentines_bg.jpg') }}'); */
            /* background-image:url('{{ asset('images/halloween_bg.jpg') }}'); */
            /* background-image:url('{{ asset('images/christmas_bg3.jpg') }}');  */
            background-size: cover;
        }
        body{
            -webkit-text-size-adjust: auto!important;
        }
        @media screen and (min-width: 1200px) {
            #monsterland_logo{
                width:193px;
            }
        }
        /*@media only screen and (max-width:900px) and (orientation:portrait){
            .pageWarning{
                display:block!important;
            }
        }*/
        html,
        body{
            overscroll-behavior-y: none!important;
        }
    </style>

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-W5CT252');</script>
    <!-- End Google Tag Manager -->

    <script type="text/javascript">
    var prevModalActive = false;
    $(document).ready(function(){
       setZoom();

       setInterval(function(){
            modalActive = $('.modal').length > 0;
            if (modalActive != prevModalActive){
                checkForModals(modalActive);
                prevModalActive = modalActive;
            }
        },100);
    })

    function setZoom(){
        var zoom;
        if (isIOS()){
            zoom = window.innerWidth/1000;
        } else {
            zoom = screen.availWidth/1000;
        }
        zoom = zoom < 1 ? zoom : 1;
        if (zoom == 1) return;

        document.body.style.transform = "scale(" + zoom + ")";
        document.body.style.MozTransform = "scale(" + zoom + ")";
        document.body.style.msTransform = "scale(" + zoom + ")";
        document.body.style.OTransform = "scale(" + zoom + ")";
        document.body.style.webkitTransform = "scale(" + zoom + ")";
        document.body.style.transformOrigin = "top left";
        document.body.style.width = (100 / zoom) + "%";
        document.body.style.height = (100 / zoom) + "%";

        // document.body.style.transform = 'scale(' + (zoom < 1 ? zoom : 1) + ')';
        var btn = $('#main-container > div:nth-child(2) > div.row.mb-2 > div.col-7 > div.colorPicker.black.selected > button');

        var div =$('#main-container > div.container-xl.mt-3');
        // div.append('<div style="color:lightgrey;">w=' + btn.css('width') + ' ' + 'h=' + btn.css('height') + '</div>');
    }

    function cancelZoom(){
        document.body.style.transform = "none";
        document.body.style.MozTransform = "none";
        document.body.style.msTransform = "none";
        document.body.style.OTransform = "none";
        document.body.style.webkitTransform = "none";
        document.body.style.transformOrigin = "top left";
        document.body.style.width = "100%";
        document.body.style.height = "100%";
    }

    function isIOS(){
        var inBrowser = typeof window !== 'undefined';
        var inWeex = typeof WXEnvironment !== 'undefined' && !!WXEnvironment.platform;
        var weexPlatform = inWeex && WXEnvironment.platform.toLowerCase();
        var UA = inBrowser && window.navigator.userAgent.toLowerCase();
        var ios = (UA && /iphone|ipad|ipod|ios/.test(UA)) || (weexPlatform === 'ios');

        return ios;
    }

    window.addEventListener("orientationchange", function(event) {
        if (isIOS()){
            setZoom();
        } else {
            location.reload();
        }
    });

    function checkForModals(modalActive){
        if (modalActive){
            cancelZoom();
        } else {
            setZoom();
        }
    }

    // window.addEventListener("resize", function(event) {
    //     setZoom();
    // });
    </script>

</head>
<body id="pageContainer" style="">
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
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
                        <li class="nav-item">
                            <a class="nav-link" href="/monstergrid/mymonsters">My Monsters</a>
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
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                                @if (Route::has('register'))
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                    </li>
                                @endif
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    @if (Auth::user()->vip == 1)
                                        <i class="fa fa-star" title="VIP member"></i> 
                                    @endif
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
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
            <div class="alert alert-warning pageWarning">Rotate your screen. It's easier to draw in landscape mode.</div>
            @yield('content')
        </main>
    </div>
</body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-W5CT252"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
</html>
