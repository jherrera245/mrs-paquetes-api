<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use Otp;
use Hash;

class ResetPasswordController extends Controller
{
    private $otp;

    public function __construct()
    {
        $this->otp = new Otp;
    }

    public function passwordReset(ResetPasswordRequest $request)
    {
        $otp2 = $this->otp->validate($request->email, $request->otp );
        if (! $otp2->status) {
           return response()->json(['error' => $otp2], 401);
        }
        $user = User::where('email', $request->email)->first();
        $user->Update(['password' => Hash::make($request->password)]);
        $success['success']=true;
        return response()->json($success,200);
    }
}
