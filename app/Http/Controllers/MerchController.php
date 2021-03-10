<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\PrintfulMockup;

class MerchController extends Controller
{
    public function index()
    {
        $monster_id = 16815;
        $task_key = "za6295ac9269c4cea9a248de3d4b831b";


        $response = $this->mockupGenerationComplete($task_key);
        $complete = false;
        if ($response->successful()){
            $result = $response->json()['result'];
            $status = $result['status'];
            if ($status == 'completed'){
                foreach ($result['mockups'] as $mockup){
                    $printfulMockup = new PrintfulMockup;
                    $printfulMockup->monster_id = $monster_id;
                    $printfulMockup->url = $mockup['mockup_url'];  
                    $printfulMockup->save();
                }
                $complete = true;
            }
        }




        $this->generateMockup();

        $apiKey = '8ry9ia02-8led-w8n5:36za-6uvhz4t5di55';
        $apiKey = base64_encode($apiKey);
        $authHeader = 'Basic ' . $apiKey;

        $sync_product = [
            "name" => "Short Sleeve Unisex Shirt",
            "thumbnail" => "https://monsterland.net/storage/16815.png"
        ];
        $sync_variants = [
            [
                "retail_price"=> "21.00",
                "variant_id"=> 4011,
                "files" => [
                    [
                        "url"=> "https://monsterland.net/storage/16815.png"
                    ],
                    [
                        "type"=> "back",
                        "url"=> "https://monsterland.net/storage/16815.png"
                    ]
                ]
            ],
            [
                "retail_price"=> "21.00",
                "variant_id"=> 4012,
                "files"=> [
                    [
                        "url"=> "https://monsterland.net/storage/16815.png"
                    ],
                    [
                        "type"=> "back",
                        "url"=> "https://monsterland.net/storage/16815.png"
                    ]
                ]
            ]
        ];

        $payload = [
            "sync_product" => $sync_product,
            "sync_variants" => $sync_variants
        ];

        $response = Http::withHeaders([
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ])
        ->withToken($authHeader, null)
        ->post('https://api.printful.com/store/products',  $payload);

        dd($response->json());
 
    }

    function generateMockup (){
        $apiKey = '8ry9ia02-8led-w8n5:36za-6uvhz4t5di55';
        $apiKey = base64_encode($apiKey);
        $authHeader = 'Basic ' . $apiKey;

        $payload = [
            "variant_ids" => [4012, 4013, 4014, 4017, 4018, 4019],
            "format" => "jpg",
            "files" => [
                [
                    "placement" => "front",
                    "image_url" => "https://monsterland.net/storage/16815.png",
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

        // $payload = [
        //     "sync_product" => $sync_product,
        //     "sync_variants" => $sync_variants
        // ];

        $response = Http::withHeaders([
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ])
        ->withToken($authHeader, null)
        ->post('https://api.printful.com/mockup-generator/create-task/71',  $payload);

        if ($response->successful()){
            $result = $response->json()['result'];
            $task_key = $result['task_key'];
        }
        die();
    }

    function mockupGenerationComplete($task_key){

        $apiKey = '8ry9ia02-8led-w8n5:36za-6uvhz4t5di55';
        $apiKey = base64_encode($apiKey);
        $authHeader = 'Basic ' . $apiKey;

        $response = Http::withHeaders([
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ])
        ->withToken($authHeader, null)
        ->get("https://api.printful.com/mockup-generator/task?task_key=$task_key");

        return $response;
        
    }
}
