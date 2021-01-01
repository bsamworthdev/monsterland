<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\DBRandomWordsRepository;
use App\Models\RandomWord;
use Illuminate\Support\Facades\Log;

class RandomWordsController extends Controller
{
    protected $DBRandomWordsRepo;

    public function __construct(Request $request, 
        DBRandomWordsRepository $DBRandomWordsRepo)
    {
        $this->middleware(['auth','verified']);
        $this->DBRandomWordsRepo = $DBRandomWordsRepo;
    }

    public function index()
    {
        $random_words = $this->DBRandomWordsRepo->getAll();
        return view('randomWords', [
            "random_words" => $random_words
        ]);
    }

    public function create(Request $request)
    {
        
        $wordType = $request->type;
        $wordText = trim($request->word);
        if ($wordText == "" || strlen($wordText) > 20){
            die();
        }

        $randomWord = new RandomWord;
        $randomWord->word = $wordText;
        $randomWord->type = $wordType;
        $randomWord->save();

        return redirect()->back()->with('message', 'Added new '.$wordType.': '.$wordText);

    }
    public function delete(Request $request)
    {
        $wordType = $request->type;
        $wordText = trim($request->word);

        RandomWord::where('word',$wordText)
            ->where('type',$wordType)
            ->delete();

        return redirect()->back()->with('message', 'Deleted '.$wordType.': '.$wordText);
    }
}
