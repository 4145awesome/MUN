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
                $response = $client->request('POST', $this->insuranceUrl, ['form_params' => ['mlsid' => $MlsID, 'munCode' => $code->muncode]]);
                $error = false;
                if($response->getStatusCode() != 200){
                    $error = true;
                    $body = $response->getReasonPhrase();
                }else{
                    $body = $response->getBody();
                }
                return response()->json(["error" => $error, "response" => json_decode((string) $body)]);
            }
        }else{
            return response()->json(["error" => "Could not find code"], 500);
        }
    }
}
