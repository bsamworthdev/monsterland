<?php

namespace app\Repositories;

use App\Models\Book;

class DBBookRepository{

  function createBook($user_id, $group, $monsters){
    $book = new Book;
    $book->user_id = $user_id;
    $book->group_id = $group->id;
    $book->title = $group->name;
    $book->save();
    
    $book->monsters()->detach();
    $book->monsters()->sync($monsters);
    $book->save();

    return $book->id;
  }

  function getBook($user_id, $book_id){
    $book = Book::where('id',$book_id)
      ->when($user_id <> 1, function($q) use ($user_id){
        $q->where('user_id', $user_id);
      })->first();
    return $book;
  }
}