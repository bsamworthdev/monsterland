<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

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

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script type="text/javascript" src="https://platform-api.sharethis.com/js/sharethis.js#property=5f67b48601b108001af03582&product=image-share-buttons" async="async"></script>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        #includeNSFW{
            margin-left:0!important;
            
        }
        .nav-item.settings{
            margin-right:40px;
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
<body style="background-image:url('{{ asset('images/countryside_bg.jpg') }}'); background-repeat: no-repeat;
background-size: cover;background-attachment: fixed;">
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/home') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

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
                            <a class="nav-link" href="/monsters/{{Auth::user()->id}}">My Monsters</a>
                        </li>
                        @endguest
                        <li class="nav-item">
                            <a class="nav-link" href="/gallery">Gallery</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/halloffame">Hall Of Fame</a>
                        </li>
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
                            <li class="nav-item settings">
                                <a class="nav-link" id="includeNSFWLink">
                                    <label for="includeNSFW" title="Show 'Not Safe For Work' monsters">Show NSFW
                                        <input type="checkbox" id="includeNSFW" {{ Auth::user()->allow_nsfw == 1 ? 'checked' : '' }} onclick="includeNSFW_clicked(event)" class="form-check-input">
                                    <label>
                                </a>
                            </li>
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
            @yield('content')
        </main>
    </div>
</body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-W5CT252"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
</html>