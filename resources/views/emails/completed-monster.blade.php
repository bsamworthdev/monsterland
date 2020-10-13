<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Your monster has been finished!</title>
</head>
<body>
    <p>Hi {{ $user->name }},</p>
    <p>Your monster has been finished!</p>
    <p>
      May I present...
    </p>
    <h2>
        {{ $monster->name }}
    </h2>
    <p>
      <a href="{{ URL::to('/') }}/gallery/{{ $monster->id }}">
        {{ URL::to('/') }}/gallery/{{ $monster->id }}
      </a>
    </p>
    <p>
        Ben at Monsterland!
    </p>
</body>
</html>                
                    
                