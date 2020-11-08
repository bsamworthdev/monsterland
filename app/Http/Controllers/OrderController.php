<?php

namespace App\Http\Controllers;

use Stripe\Stripe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use App\Models\Order;
use App\Models\OrderAddress;
use App\Models\Book;
use App\Models\User;
use App\Mail\OrderedBookCustomerMailable;
use App\Mail\OrderedBookAdminMailable;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }

    //
    function index(Request $request){

        $order = Order::find(15);  

        $user_id = Auth::User()->id;
        $book_cost = 550;
        $delivery_cost = 599;
        $quantity = $request->quantity;
        $book_id = $request->bookId;
        $title = $request->title;
        $firstname = $request->address['firstname'];
        $surname = $request->address['surname'];
        $address1 = $request->address['address1'];
        $address2 = $request->address['address2'];
        $town = $request->address['town'];
        $postcode = $request->address['postcode'];
        $email = $request->address['email'];
        $phone = $request->address['phone'];
        $total_cost = ($quantity*$book_cost) + $delivery_cost;
        
        // $order = Order::create( [
        //     'user_id' => $user_id, 
        //     'book_id' => $book_id,
        //     'quantity' => $quantity,
        //     'total_cost' => $total_cost/100
        // ]);

        $order = new Order;
        $order->user_id = $user_id;
        $order->book_id = $book_id;
        $order->quantity = $quantity;
        $order->total_cost = $total_cost/100;
        $order->save();
        $order_id = $order->id;

        $orderAddress = new OrderAddress;
        $orderAddress->order_id = $order_id;
        $orderAddress->firstname = $firstname;
        $orderAddress->surname = $surname;
        $orderAddress->address1 = $address1;
        $orderAddress->address2 = $address2;
        $orderAddress->town = $town;
        $orderAddress->postcode = $postcode;
        $orderAddress->email = $email;
        $orderAddress->phone = $phone;
        $order->address()->save($orderAddress);

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

            $book = Book::find($book_id);   
            $user = User::find($user_id);   
            $order = Order::find($order_id);  

            //Email the customer    
            Mail::to($order->address->email)
                ->send(new OrderedBookCustomerMailable($book, $user, $order));

            //Email administrator    
            Mail::to('admin@monsterland.net')
                ->send(new OrderedBookAdminMailable($book, $user, $order));

            return view('payment.success');
        } elseif ($result == 'cancel'){
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