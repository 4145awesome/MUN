<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class MunController extends Controller
{
    //address of insurance company endpoint
    private $insuranceUrl = 'https://insinc.laboratory.cf/muncode';

    /**
     * @param $MlsID - The property ID, retrieved from the URL
     * @param Request $request - Ued to get form data
     * @return Response - JSON-formatted response
     */
    public function getProperty($MlsID, Request $request){
        //get the code from the table
        $code = app('db')->table('codes')->select('muncode')->where('mlsid', $MlsID)->first();
        //get the mortID from the request
        $mortID = $request->input('mortID');

        //if we were able to find a code
        if($code){
            //prepare data
            $toSend = ["mlsid" => $MlsID, "mortID" => $mortID, 'munCode' => $code->muncode];
            //if debugging is on
            if($request->input('debug') == 1){
                //return what we would send INSinc instead of actually sending it
                return response()->json(["error" => false, "response" => $toSend]);
            }else {
                //create a new GuzzleHttp client
                $client = new Client();
                try{
                    //connect to the insurance company and send the data
                    $response = $client->request('POST', $this->insuranceUrl, ['form_params' => $toSend]);
                    $error = false;

                    //if the response returns anything other than OK
                    if($response->getStatusCode() != 200){
                        //set error and set reason
                        $error = true;
                        $body = $response->getReasonPhrase();
                    }else{
                        //get content of response
                        $body = json_decode((string) $response->getBody());
                    }
                } catch (\Exception $e) { //if the request fails
                    //set error and message
                    $error = true;
                    $body = $e->getMessage();
                }

                return response()->json(["error" => $error, "response" =>  $body]);
            }
        }else{ //db request returned 0 rows
            return response()->json(["error" => "Could not find property"], 500);
        }
    }
}
