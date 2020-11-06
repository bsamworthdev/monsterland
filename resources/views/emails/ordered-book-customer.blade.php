<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Order Received</title>
</head>
<body>
    <p>Hello {{ $user->name }},</p>
    <p>Your order for the following has been received:</p>
    <p>
      <table>
        <tr>
          <th>Item(s)</th>
          <th>Qty</th>
        </tr>
        <tr>
          <td>Monsterland Book</td>
          <td style="text-align:center">{{ $order->quantity }}</td>
        </tr>
        <tr>
          <td><b>Total Cost</b></td>
          <td style="text-align:right">{{ $order->total_cost }}</td>
        </tr>
      </table>
      
    </p>
    <p>
      Regards,
    </p>
    <p>
        Ben at Monsterland!
    </p>
</body>
</html>                
                    
                