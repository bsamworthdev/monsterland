<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\PrintfulMockup;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class MerchController extends Controller
{

    protected $api_key;

    public function __construct(Setting $setting)
    {
        $this->middleware(['auth','verified']);
        $this->api_key = $setting->where('name','printful_api_key')->value('value');
    }

    public function index($monster_id)
    {
        $mockup = PrintfulMockup::where('monster_id',$monster_id)->first();
        if (!$mockup){
            $task_key = $this->generateMockup($monster_id);
            if ($task_key == '') die;

            $printfulMockup = new PrintfulMockup;
            $printfulMockup->monster_id = $monster_id;
            $printfulMockup->status = 'pending';
            $printfulMockup->url = '';  
            $printfulMockup->task_key = $task_key;
            $printfulMockup->save();
        } else {
            $task_key = $mockup->task_key;
            $response = $this->mockupGenerationComplete($task_key);
            $complete = false;
            if ($response->successful()){
                $result = $response->json()['result'];
                $status = $result['status'];
                if ($status == 'completed'){
                    PrintfulMockup::where('monster_id',$monster_id)->delete();
                    foreach ($result['mockups'] as $mockup){
                        $printfulMockup = new PrintfulMockup;
                        $printfulMockup->monster_id = $monster_id;
                        $printfulMockup->status = 'created';
                        $printfulMockup->url = $mockup['mockup_url'];  
                        $printfulMockup->task_key = 'NULL';
                        $printfulMockup->save();
                    }
                    $complete = true;
                }
            } else {
                //Not completed yet

            }
        }

        // $apiKey = base64_encode($this->api_key);
        // $authHeader = 'Basic ' . $apiKey;

        // $sync_product = [
        //     "name" => "Short Sleeve Unisex Shirt",
        //     "thumbnail" => "https://monsterland.net/storage/16815.png"
        // ];
        // $sync_variants = [
        //     [
        //         "retail_price"=> "21.00",
        //         "variant_id"=> 4011,
        //         "files" => [
        //             [
        //                 "url"=> "https://monsterland.net/storage/16815.png"
        //             ],
        //             [
        //                 "type"=> "back",
        //                 "url"=> "https://monsterland.net/storage/16815.png"
        //             ]
        //         ]
        //     ],
        //     [
        //         "retail_price"=> "21.00",
        //         "variant_id"=> 4012,
        //         "files"=> [
        //             [
        //                 "url"=> "https://monsterland.net/storage/16815.png"
        //             ],
        //             [
        //                 "type"=> "back",
        //                 "url"=> "https://monsterland.net/storage/16815.png"
        //             ]
        //         ]
        //     ]
        // ];

        // $payload = [
        //     "sync_product" => $sync_product,
        //     "sync_variants" => $sync_variants
        // ];

        // $response = Http::withHeaders([
        //             'Content-Type' => 'application/x-www-form-urlencoded',
        //         ])
        // ->withToken($authHeader, null)
        // ->post('https://api.printful.com/store/products',  $payload);

        // dd($response->json());
 
    }

    function generateMockup ($monster_id){
        $apiKey = base64_encode($this->api_key);
        $authHeader = 'Basic ' . $apiKey;

        $payload = [
            "variant_ids" => [4012, 4013, 4014, 4017, 4018, 4019],
            "format" => "jpg",
            "files" => [
                [
                    "placement" => "front",
                    "image_url" => "https://monsterland.net/storage/$monster_id.png",
                    "position" => [
                        "area_width" => 1800,
                        "area_height" => 1800,
                        "width" => 1800,
                        "height" => 1800,
                        "top" => 300,
                        "left" => 0
                    ]
                ]
            ]
        ];

        $response = Http::withHeaders([
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ])
        ->withToken($authHeader, null)
        ->post('https://api.printful.com/mockup-generator/create-task/71',  $payload);

        $task_key = '';
        if ($response->successful()){
            $result = $response->json()['result'];
            $task_key = $result['task_key'];
        }
        return $task_key;
    }

    function mockupGenerationComplete($task_key){

        $apiKey = base64_encode($this->api_key);
        $authHeader = 'Basic ' . $apiKey;

        $response = Http::withHeaders([
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ])
        ->withToken($authHeader, null)
        ->get("https://api.printful.com/mockup-generator/task?task_key=$task_key");

        return $response;
        
    }
}
