<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class MunController extends Controller
{

    private $insuranceUrl = 'https://insinc.laboratory.cf/muncode';

    public function getProperty($MlsID, Request $request){
        $code = app('db')->table('codes')->select('muncode')->where('mlsid', $MlsID)->first();
        $mortID = $request->input('mortID');
        if($code){
            if($request->input('debug') == 1){
                return response()->json(["error" => false, "response" =>["mlsid" => $mortID, 'munCode' => $code->muncode]]);
            }else {
                $client = new Client();
                $response = $client->request('POST', $this->insuranceUrl, ['form_params' => ['mortId' => 12443, 'munCode' => $code->muncode]]);
                $error = false;
                $body = "No Data";
                if($response->getStatusCode() != 200){
                    $error = true;
                    $body = $response->getReasonPhrase();
                }else{
                    $body = $response->getBody();
                }
                return response()->json(["error" => false, "response" => json_decode((string) $body)]);
            }
        }else{
            return response()->json(["error" => "Could not find code"], 500);
        }
    }
}
