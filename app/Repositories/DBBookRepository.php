<?php

namespace app\Repositories;

use App\Models\Book;

class DBBookRepository{

  function save($user_id, $monsters){
    $book = new Book;
    $book->user_id = $user_id;
    $book->save();
    
    $book->monsters()->detach();
    $book->monsters()->sync($monsters);
    $book->save();

    return $book->id;
  }
}