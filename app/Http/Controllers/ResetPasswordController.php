<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserPasswordPin;

class ResetPasswordController extends Controller
{
    //avoid predictable pins
    private $unwantedPins = [
        111111,222222,333333,444444,555555,666666,777777,888888,999999
    ];

    private function inUnWanted($pin)
    {
        return in_array($pin,$this->unwantedPins);
    }

    public function recoverPassword(Request $request)
    {


        $this->validate($request, [
            'email'=>'required|email',
        ]);

        //check if email exist
        $user = User::where(['email' => $request->email])->first();

        if (is_null($user)){
            return response([
                'errors'=>'The email entered is not registered'
            ],404);
        }

        //send recover password code like six digit code to the recovery email
        $pin = rand( 111111,999999);
        foreach ($this->unwantedPins as $unwantedPin){
            if ($pin == $unwantedPin){
                $pin = rand(111111,999999);
            }
        }
        $user->pin = $pin;//save pin to database
        $user->save();

        // Mail::to($user)->queue(new UserPasswordPin($pin));
        return response([
            'pin'=>$pin,
            'message'=>'a six digit code has been sent to your email'
        ],200);
    }

    public function changePassword(Request $request)
    {
        $this->validate($request, [
            'pin'=>'required|min:6|numeric',
            'password'=>'required|min:6'
        ]);


        // find user by pin
        //change password with the new one
        $user = User::where(['pin' => $request->pin])->first();
        if (is_null($user)){
            return response([
                'errors'=>'The pin entered is incorrect, check your email'
            ],404);
        }
        $user->password = Hash::make($request['password']);
        $user->pin = null;//set pin to null after using it
        $user->save();
        return response([
            'success'=>true,
            'message'=>'please login with your new password'
        ],200);

    }
}
