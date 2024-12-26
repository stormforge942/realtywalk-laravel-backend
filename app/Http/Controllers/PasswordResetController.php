<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Notifications\PasswordResetRequest;
use App\Notifications\PasswordResetSuccess;
use App\User;
use App\Models\PasswordReset;

class PasswordResetController extends AppBaseController
{
    /**
     * Create token password reset
     *
     * @param  [string] email
     * @return [string] message
    */
    public function sendResetLink(Request $request) {
        $rules = array (
            'email' => 'required|string|email',
            recaptchaFieldName() => recaptchaRuleName(),
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            $errorString = implode(",",$validator->messages()->all());
            return $this->respondWithError($errorString);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user){
            return $this->respondWithError('We can\'t find a user with this email address');
        }

        if (is_null($user->email_verified_at)) {
            return $this->respondWithError(__('auth.email_is_not_verified'));
        }

        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => $this->generateToken(20)
             ]
        );

        if ($user && $passwordReset) {
            $user->notify(
                new PasswordResetRequest($passwordReset->token)
            );

            return $this->respond("We have emailed your password reset link!");
        }
    }

    public function confirmToken($token) {
        if($token === ""){
            return $this->respondWithError('This password reset token is invalid.');
        }

        $passwordReset = PasswordReset::where('token', $token)
                        ->first();

        if (!$passwordReset) {
            return $this->respondWithError('This password reset token is invalid.');
        }

        if (Carbon::parse($passwordReset->updated_at)->addMinutes(10)->isPast()) {
            $passwordReset->delete();
            return $this->respondWithError('This password reset token has expired.');
        }

        return $this->respond($passwordReset);
    }

    public function resetPassword(Request $request) {
        /**
             * Reset password
             *
             * @param  [string] email
             * @param  [string] password
             * @param  [string] password_confirmation
             * @param  [string] token
             * @return [string] message
             * @return [json] Response
        */

        $messages = array(
            'required' => 'All fields are required',
            'same' => 'Passwords do not match',
        );

        $validator = Validator::make($request->all(), [
            'resetToken'        => 'required',
            'email'             => 'required|string|email',
            'password'          => 'required|string',
            'cpassword'         => 'required|same:password',
        ], $messages);

        if($validator->fails()){
            $errorString = implode(",",$validator->messages()->all());
            return $this->respondWithError($errorString);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return $this->respondWithError('We can\'t find a user with this e-mail address');
        }

        $passwordReset = PasswordReset::where([
            ['token', $request->resetToken],
            ['email', $request->email]
        ])->first();

        if (!$passwordReset) {
            return $this->respondWithError('This password reset token is invalid.');
        }

        $user->forceFill([
            'password' => $request->password, //Removed bcrypt]
        ])->save();
        $passwordReset->delete();
        $user->notify(new PasswordResetSuccess($passwordReset));

        return $this->respond('Your password has been changed successfully!');
    }
}
