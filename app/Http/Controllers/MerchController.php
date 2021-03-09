<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MerchController extends Controller
{
    public function index()
    {
        // $response = Http::withHeaders([
        //     'Authorization:' => 'Basic 8ry9ia02-8led-w8n5:36za-6uvhz4t5di55',
        // ])->post('https://api.printful.com/store/products', [
        //     'request body' => '{
        //         "sync_product": {
        //             "name": "API product Bella",
        //             "thumbnail": "https://monsterland.net/storage/16815a.png"
        //         },
        //         "sync_variants": [
        //             {
        //                 "retail_price": "21.00",
        //                 "variant_id": 4011,
        //                 "files": [
        //                     {
        //                         "url": "https://monsterland.net/storage/16815b.png"
        //                     },
        //                     {
        //                         "type": "back",
        //                         "url": "https://monsterland.net/storage/16815c.png"
        //                     }
        //                 ]
        //             },
        //             {
        //                 "retail_price": "21.00",
        //                 "variant_id": 4012,
        //                 "files": [
        //                     {
        //                         "url": "https://monsterland.net/storage/16815d.png"
        //                     },
        //                     {
        //                         "type": "back",
        //                         "url": "https://monsterland.net/storage/16815e.png"
        //                     }
        //                 ]
        //             }
        //         ]
        //     }'
        // ]);


        // $apiKey = '8ry9ia02-8led-w8n5:36za-6uvhz4t5di55';
        // $authHeader = ['Basic ' . $apiKey];

        // $response = Http::withHeaders([
        //             'accept' => 'application/json'
        //         ])
        // ->withToken($authHeader, null)
        // ->post('https://api.printful.com/store/products');






        // $apiKey = '8ry9ia02-8led-w8n5:36za-6uvhz4t5di55';
        // $apiKey = base64_encode($apiKey);
        // $authHeader = ['Authorization: Basic ' . $apiKey];
        // $payload = '{
        //             "sync_product": {
        //                 "name": "API product Bella",
        //                 "thumbnail": "https://monsterland.net/storage/16815a.png"
        //             },
        //             "sync_variants": [
        //                 {
        //                     "retail_price": "21.00",
        //                     "variant_id": 4011,
        //                     "files": [
        //                         {
        //                             "url": "https://monsterland.net/storage/16815b.png"
        //                         },
        //                         {
        //                             "type": "back",
        //                             "url": "https://monsterland.net/storage/16815c.png"
        //                         }
        //                     ]
        //                 },
        //                 {
        //                     "retail_price": "21.00",
        //                     "variant_id": 4012,
        //                     "files": [
        //                         {
        //                             "url": "https://monsterland.net/storage/16815d.png"
        //                         },
        //                         {
        //                             "type": "back",
        //                             "url": "https://monsterland.net/storage/16815e.png"
        //                         }
        //                     ]
        //                 }
        //             ]
        //         }';
        



        
        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, 'https://api.printful.com/store/products');
        // curl_setopt($ch, CURLOPT_HTTPHEADER, $authHeader);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        // $response = curl_exec($ch);
        // $response = json_decode($response);




        
        $apiKey = '8ry9ia02-8led-w8n5:36za-6uvhz4t5di55';
        $apiKey = base64_encode($apiKey);
        $authHeader = 'Basic ' . $apiKey;
        $payload = '
                        "sync_product": {
                            "name": "API product Bella",
                            "thumbnail": "https://monsterland.net/storage/16815a.png"
                        },
                        "sync_variants": [
                            {
                                "retail_price": "21.00",
                                "variant_id": 4011,
                                "files": [
                                    {
                                        "url": "https://monsterland.net/storage/16815b.png"
                                    },
                                    {
                                        "type": "back",
                                        "url": "https://monsterland.net/storage/16815c.png"
                                    }
                                ]
                            },
                            {
                                "retail_price": "21.00",
                                "variant_id": 4012,
                                "files": [
                                    {
                                        "url": "https://monsterland.net/storage/16815d.png"
                                    },
                                    {
                                        "type": "back",
                                        "url": "https://monsterland.net/storage/16815e.png"
                                    }
                                ]
                            }
                        ]
                    ';

                     $payload = json_decode($payload);


        $response = Http::withHeaders([
                    'accept' => 'application/json',
                    'Access-Control-Allow-Origin' => '*'
                ])
        ->withToken($authHeader, null)
        ->post('https://api.printful.com/store/products',  [$payload]);

        dd($response);
    }
}
