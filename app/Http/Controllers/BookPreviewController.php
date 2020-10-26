<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Repositories\DBMonsterRepository;
use App\Repositories\DBUserRepository;
use App\Repositories\DBBookRepository;

class BookPreviewController extends Controller
{

    protected $DBMonsterRepo;
    protected $DBUserRepo;
    protected $DBBookRepo;

    public function __construct(Request $request, 
    DBMonsterRepository $DBMonsterRepo,
    DBUserRepository $DBUserRepo,
    DBBookRepository $DBBookRepo)
    {
        $this->DBMonsterRepo = $DBMonsterRepo;
        $this->DBUserRepo = $DBUserRepo;
        $this->DBBookRepo = $DBBookRepo;
    }
    public function index($book_id)
    {
        if (Auth::check()){
            $user_id = Auth::User()->id;

            $book = $this->DBBookRepo->getBook($user_id, $book_id);

            $monsters = $book->monsters;
            $bookTitle = $book->title;
            return view('bookPreview',[
                'monsters' => $monsters,
                'bookTitle' => $bookTitle
            ]);
        }
    }
}
