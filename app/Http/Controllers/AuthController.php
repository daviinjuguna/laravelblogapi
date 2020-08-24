<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Client;

class AuthController extends Controller
{
    use IssueTokenTrait;
    private $client;

    public function __construct()
    {
        $this->client = Client::find(2);
    }


    public function login(Request $request)
    {
        $credentials = $this->validate($request, [
            'email' => 'required',
            'password' => 'required'

        ]);
        $authAttempt = Auth::attempt($credentials);
        if (!$authAttempt){
            return response([
                'error'=>'forbidden',
                'message'=>'Check your Credentials'
            ],403);
        }else{
            return $this->issueToken($request,'password');

        }

    }

    public function logout( Request $request)
    {
        $accessToken = Auth::user()->token();
        DB::table('oauth_refresh_tokens')
            ->where('access_token_id',$accessToken->id)
            ->update(['revoked'=>true]);

        $accessToken->revoke();
        return response()->json('Logged Out Successfully',204);
    }

    public function refresh(Request $request)
    {
        $this->validate($request, [
            'refresh_token' => 'required'

        ]);
        return $this->issueToken($request,'refresh_token');

    }
    public function register(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        User::create([
            'email' => $request['email'],
            'password' => Hash::make($request['password'])
        ]);

        return $this->issueToken($request, 'password');
    }

    public function saveUserInfo(Request $request){
        $user = User::find(Auth::user()->id);
        $user->name = $request->name;
        $user->last_name = $request->last_name;
        $photo = '';
        //check if user provided photo
        if($request->photo!=''){
            // user time for photo name to prevent name duplication
            $photo = time().'.jpg';
            // decode photo string and save to storage/profiles
            file_put_contents('storage/profiles/'.$photo,base64_decode($request->photo));
            $user->photo = $photo;
        }

        $user->update();

        return response()->json([
            'success' => true,
            'photo' => $photo
        ]);

    }
}
