@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">

                <div class="card-header">
                    <h4>Mobile App</h4>
                </div>
                <div class="card-body">
                   <h2>Monsterland now has a mobile app!</h2>
                   <p>
                       It is available to download for <b>android</b> and </b>ios</b> devices, both <b>mobile</b> and <b>tablet</b>.
                   </p>
                   <p>
                    <h4>Apple Store (ios)</h4>
                    <a href="https://apps.apple.com/gb/app/monster-land/id1547280278">
                        https://apps.apple.com/gb/app/monster-land/id1547280278
                    </a>
                     
                    <h4>Play Store (Android)</h4>
                    <a href="https://play.google.com/store/apps/details?id=web2application.a471481609021114.com.myapplication">
                        https://play.google.com/store/apps/details?id=web2application.a471481609021114.com.myapplication
                    </a>
                   </p>
                   <p>
                        <div class="container">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <a href="https://apps.apple.com/gb/app/monster-land/id1547280278">
                                        <img src="{{ asset('images/ios.png') }}" class="img-fluid w-100">
                                    </a>
                                </div>
                                <div class="col-12 col-md-6">
                                    <a href="https://play.google.com/store/apps/details?id=web2application.a471481609021114.com.myapplication">
                                        <img src="{{ asset('images/android.png') }}" class="img-fluid w-100">
                                    </a>
                                </div>
                            </div>
                        </div>
                   </p>
                   <p>
                     Includes the following features:
                    <ul>
                        <li>Collaborate with thousands of other artists over the internet</li>
                        <li>Private Groups to draw with friends</li>
                        <li>24-color pallette, 5 brush-sizes</li>
                        <li>Background colour selector</li>
                        <li>Rating system</li>
                        <li>Trophies and competitions</li>
                    </ul>
                    The app is also <b>fully connected with monsterland website</b>, so you can use the same login and 
                    monsters can be shared easily across platforms.
                   </p>
                   <p>
                       <b style="font-size:18px">Happy Drawing!</b>
                   </p>
                   <p>
                       <b style="font-size:18px">Ben</b>
                       <br>
                       <a href="mailto:admin@monsterland.net">
                            admin@monsterland.net
                       </a>
                   </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection