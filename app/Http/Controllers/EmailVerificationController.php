<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\EmailVerificationRequest;
use Otp;
use App\Models\User;
use App\Notifications\EmailVerificationNotification;

class EmailVerificationController extends Controller
{
    private  $otp;

    public function __construct(){
         $this->otp = new Otp;
    }

    public function sendEmailVerification(Request $request) {
        //validamos el email con la variable request
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);
        //utilizamoa el email como dato de entrada y que exista dentro de la tbl
        $user = User::where('email', $request->input('email'))->first();
        //envia el correo al usuario
        $user->notify(new EmailVerificationNotification());

        // Return success response
        $success['success'] = true;
        return response()->json($success, 200);
    }

    public function email_verification(EmailVerificationRequest $request){
        $otp2 = $this->otp->validate($request->email, $request->otp);
    if(!$otp2->status){
        return response()->json(['error'=> $otp2],401);
    }
    $user = User::where('email',$request->email)->first();
    $user->update(['email_verified_at'=>now()]);
    $user->save();
    $success['success']= true;
    return response()->json($success,200);
    }
}
