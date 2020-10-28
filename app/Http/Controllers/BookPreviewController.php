<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Repositories\DBMonsterRepository;
use App\Repositories\DBUserRepository;
use App\Repositories\DBBookRepository;
use Illuminate\Support\Facades\Log;

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
            return view('bookPreview',[
                'monsters' => $monsters,
                'book' => $book,
            ]);
        }
    }

    public function update(Request $request)
    {
        $book_id = $request->bookId;
        $field = $request->field;
        $value = $request->value;

        if (Auth::check()){
            $user_id = Auth::User()->id;

            $book = $this->DBBookRepo->updateBook($user_id, $book_id, $field, $value);
        }
    }
}
