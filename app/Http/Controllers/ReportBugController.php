<?php

namespace App\Http\Controllers;

use App\Mail\BugReport;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ReportBugController extends Controller
{
    public function submit(Request $request)
    {
        $rules = [
            'name'  => 'required',
            'email' => 'required|email',
            'url'   => 'required|url',
            'body'  => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->respondWithError(
                implode(",", $validator->messages()->all())
            );
        }

        $mailTo = Setting::getBy('notification_email');
        Mail::to($mailTo)->send(new BugReport($request->all()));

        return $this->respond("success");
    }
}
