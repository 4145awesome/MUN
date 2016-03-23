<?php

namespace App\Http\Controllers;

class MunController extends Controller
{

    private $insuranceUrl = 'https://insinc.laboratory.cf';

    public function getProperty($MlsID, Request $request){
        $code = app('db')->table('munCodes')->where('mlsid', $MlsID)->first();
        $mortID = $request->input('mortID');
        if($code){
            Request::create($this->insuranceUrl, 'POST', ['MortID' => $mortID, 'munCode' => $code]);
        }
    }
}
