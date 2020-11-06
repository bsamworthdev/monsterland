<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Order Placed</title>
</head>
<body>
    <p>An order has been placed for the following:</p>
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
      <table>
        <tr>
          <td><b>First Name: </b></td>
          <td>{{ $order->address->firstname }}</td>
        </tr>
        <tr>
          <td><b>Surname: </b></td>
          <td>{{ $order->address->surname }}</td>
        </tr>
        <tr>
          <td><b>Address 1: </b></td>
          <td>{{ $order->address->address1 }}</td>
        </tr>
        <tr>
          <td><b>Address 2: </b></td>
          <td>{{ $order->address->address2 }}</td>
        </tr>
        <tr>
          <td><b>Town: </b></td>
          <td>{{ $order->address->town }}</td>
        </tr>
        <tr>
          <td><b>Post code: </b></td>
          <td>{{ $order->address->postcode }}</td>
        </tr>
        <tr>
          <td><b>Email: </b></td>
          <td>{{ $order->address->email }}</td>
        </tr>
        <tr>
          <td><b>Phone: </b></td>
          <td>{{ $order->address->phone }}</td>
        </tr>
      </table>
    </p>
</body>
</html>                
                    
                