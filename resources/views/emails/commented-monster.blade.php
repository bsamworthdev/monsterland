<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Someone has commented on your monster</title>
</head>
<body>
    <p>Hi {{ $user->name }},</p>
    <p>Someone has commented on your monster: <b>{{ $monster->name }}</b></p>
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
                    
                