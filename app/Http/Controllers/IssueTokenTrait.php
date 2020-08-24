<?php
/**
 * Created by PhpStorm.
 * User: Kim Kim
 * Date: 8/24/2020
 * Time: 12:33 AM
 */
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

trait IssueTokenTrait{

    public function issueToken(Request $request,$grantType,$scope='*')
    {
        $params =[

            'grant_type' => $grantType,
            'client_id' => $this->client->id,
            'client_secret' => $this->client->secret,
            'username' => $request->email,
            'password' => request('password'),
            'scope' => $scope
        ];

        $request->request->add($params);
        $proxy = Request::create('oauth/token', 'POST');
        return Route::dispatch($proxy);
    }
}