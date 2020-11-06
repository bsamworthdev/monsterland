<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Book;
use App\Models\User;
use App\Models\Order;

class OrderedBookAdminMailable extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $book;
    public $user;
    public $order;

    public function __construct(Book $book, User $user, Order $order)
    {
        $this->book = $book;
        $this->user = $user;
        $this->order = $order;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        //return $this->view('view.name');
        return $this->subject('Order Placed')->view('emails.ordered-book-admin');
    }
}
