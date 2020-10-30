<?php

namespace App\Http\Controllers;

use Stripe\Stripe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use App\Models\Order;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }

    //
    function index(Request $request){

        $user_id = Auth::User()->id;
        $book_cost = 500;
        $delivery_cost = 599;
        $quantity = $request->quantity;
        $book_id = $request->bookId;
        $total_cost = ($quantity*$book_cost) + $delivery_cost;
        
        $order = Order::create( [
            'user_id' => $user_id, 
            'book_id' => $book_id,
            'quantity' => $quantity,
            'total_cost' => $total_cost/100
        ]);
        $order_id = $order->id;

        Stripe::setApiKey('sk_test_51Hha0VEIt1Qs8vIVwqN8ru9so7hc1NTd9HIgQvCeI60QR6aWmSOtXpvAhgskSF57DkCxp3tTEfLLlFv0MII0zrwj00hPBlU4YL');
        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'gbp',
                    'product_data' => [
                        'name' => $quantity.' Monsterland Book'.($quantity > 1 ? 's': ''),
                        'images' => ['https://monsterland.net/images/monsterland.jpg'],
                    ],
                    'unit_amount' => $total_cost,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => URL::to('/').'/stripe/payment/success/'.$order_id.'/'.$book_id,
            'cancel_url' => URL::to('/').'/stripe/payment/cancel/'.$order_id.'/'.$book_id,
        ]);
        $response = response()->json( 
            ['id' => $session->id ]
        );
        
        return $response;
    }

    function completed($result, $order_id, $book_id){

        $user_id = Auth::User()->id;
        if ($result == 'success'){
            Order::where('id',$order_id)
                ->where('user_id', $user_id)
                ->where('book_id', $book_id)
                ->update([
                    'status' => 'completed', 
                ]);
            return view('payment.success');
        }elseif ($result == 'cancel'){
            Order::where('id',$order_id)
                ->where('user_id', $user_id)
                ->where('book_id', $book_id)
                ->update([
                    'status' => 'cancelled', 
                ]);

            header('Location: /book/preview/'.$book_id);
            die();
            //return view('payment.cancel');
        }
    }
}
