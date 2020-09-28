@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">

                <div class="card-header">
                    <h4>Trophies</h4>
                </div>
                <div class="card-body">
                    <h5>What are trophies?</h5>
                    <p>
                        You can get awarded trophies on Monsterland for completing certain achievements. 
                        They will display at the top of the page whenever you log in. You probably already have some.
                        Have a look.
                    </p>
                    <h5 class="pt-2">So how do you get them?</h5>
                    <p>Currently the standard ways to be awarded trophies are the following:</p>
                    <p>
                        <b><i class="fas fa-trophy trophy gold"></i> Gold</b>
                        <ul>
                            <li>Contributed to 100 Monsters</li>
                            <li>Rated 1000 monsters</li>
                            <li>Commented 1000 times</li>
                            <li>A monster you created is voted highest for the week</li>
                        </ul>
                    </p>
                    <p>
                        <b><i class="fas fa-trophy trophy silver"></i> Silver</b>
                        <ul>
                            <li>Contributed to 10 Monsters</li>
                            <li>Rated 100 monsters</li>
                            <li>Commented 100 times</li>
                            <li>A comment you wrote received 5 upvotes</li>
                        </ul>
                    </p>
                    <p>
                        <b><i class="fas fa-trophy trophy bronze"></i> Bronze</b>
                        <ul>
                            <li>Contributed to a Monster</li>
                            <li>Rated 10 monsters</li>
                            <li>Commented 10 times</li>
                        </ul>
                    </p>
                    <p> 
                        There are also other non-standard trophies (e.g. Winning competitions, 
                        helpful suggestions, reporting bugs, or just generally being a bro).
                    </p>
                    <h5 class="pt-2">Can I lose trophies?</h5>
                    <p>
                        Yes, you can lose trophies for generally annoying/unpleasant 
                        behaviour. e.g. Scribbling, putting NSFW content on SFW monsters, etc...
                    </p>
                    <h5 class="pt-2">Do I need an account?</h5>
                    <p>
                        Yes, you will need an account to get trophies. It's kind of impossible otherwise.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
<style>
    .card-body b{
        font-size:16px;
    }
    .trophy.gold{ color:gold; }
    .trophy.silver{ color:silver; }
    .trophy.bronze{ color:#cd7f32; }
    .trophy{    
        text-shadow: 0 0 2px #000;
    }
</style>