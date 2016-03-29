<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;

class MunController extends Controller
{

    private $insuranceUrl = 'https://insinc.laboratory.cf';

    public function getProperty($MlsID, Request $request){
        $code = app('db')->table('codes')->select('muncode')->where('mlsid', $MlsID)->first();
        $mortID = $request->input('mortID');
        if($code){
            if($request->input('debug') == 1){
                return response()->json(["error" => false, "response" =>["mortID" => $mortID, 'munCode' => $code->muncode]]);
            }else {
                Request::create($this->insuranceUrl, 'POST', ['mortID' => $mortID, 'munCode' => $code->muncode]);
                return response()->json(["error" => false]);
            }
        }else{
            return response()->json(["error" => "Could not find code"], 500);
        }
    }
}
