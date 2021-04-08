<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TagController extends Controller
{
    protected $DBTagRepo;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request, 
        DBTagRepository $DBTagRepo)
    {
            $this->DBTagRepo = $DBTagRepo;
    }

}
