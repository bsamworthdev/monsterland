@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">

                <div class="card-header">
                    <h4>About Monsterland</h4>
                </div>
                <div class="card-body">
                    <h5>What is Monsterland?</h5>
                    <p>You know that pen and paper game we have all played, where you draw the 
                        head of a monster, then fold the paper over and pass it to the next person 
                        to draw the body... and so on, until you end up with a crazy-looking and 
                        sometimes downright bizarre monster?</p>
                    <p> Monsterland is an online version of that game. The official name 
                        is <a href="https://en.wikipedia.org/wiki/Exquisite_corpse">Exquisite Corpse</a>.</p>

                    <h5 class="pt-2">So how do I play?</h5>
                    <p>It's easy. Just go to the lobby and choose a monster to draw a segment for. 
                        Or start your own new monster. (Don't forget to choose a cool name)</p>
                    <p> Here's a little guide to get you started.</p>
                    <p>
                        <div style="position:relative;padding-top:56.25%;">
                            <iframe style="position:absolute;top:0;left:0;" width="100%" height="100%" src="https://www.youtube.com/embed/wAvBrfjaROU" frameborder="1" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                    </p>
                    <h5 class="pt-2">Do I need to be a good artist?</h5>
                    <p>No. Monsterland welcomes all level of artist, from absolute beginner to seasoned pro. Just try your best.</p>
                    <p>You'll know the pros because they have little stars <i class="fa fa-star"></i> next to their names.</p>
                    
                    <h5 class="pt-2">Is Monsterland safe for kids?</h5>
                    <p>We do our best to try to keep inappropriate images flagged as "NSFW" so only users who want to see them can.</p>
                    <p>There are swearing filters to prevent bad language being used. And we try our best to monitor any 
                        new creations to make sure they are safe.</p>
                    <p>But as with any public collaborative website, it isn't always possible to guarantee some 
                        bad things might sneak through.</p>
                    <p>One other option is to request a "private group" be set up, so you can restrict the 
                        artists only to people you know.</p>
                    <h5 class="pt-2">What is a private group?</h5>
                    <p>A "private group" is a way of using Monsterland that allows you restrict the 
                        artists only to people you know,
                    <p>
                        When a private group is set up, you will be given a code, which you can distribute to your friends, 
                        classmates, work colleagues, etc... They won't even need an account to join in.
                    </p>
                    <p>
                        <div style="position:relative;padding-top:56.25%;">
                            <iframe style="position:absolute;top:0;left:0;" width="100%" height="100%" src="https://www.youtube.com/embed/rgbQz60jR5M" frameborder="1" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                    </p>

                    <h5 class="pt-2">Who created Monsterland?</h5>
                    <p>Monsterland was created by <a href="http://bensamworthdevelopment.co.uk/">Ben Samworth Development Ltd</a>. 
                        If you want to ask a question, give feedback or if you just want to say "hi" feel 
                        free to <a href="mailto:admin@monsterland.net">send us a message</a>.</p>

                    <p>The amazing logo was created by Grant Perkins <a href="http://thegrantperkins.com">thegrantperkins.com</a>. 
                        Come and check out some of his art.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection