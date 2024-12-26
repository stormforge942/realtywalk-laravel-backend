<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class BugReport extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $logo;
    public $greeting;
    public $introLines;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data       = $data;
        // $this->logo       = Storage::disk('Wasabi')->url('site/logo.png');
        $this->logo = url('images/logo-rw-horizontal.png', [], true);
        $this->greeting   = __('mail.bug_report.greeting');
        $this->subject    = __('mail.bug_report.subject');
        $this->introLines = __('mail.bug_report.lines', [
            'name'  => $data['name'],
            'email' => $data['email'],
            'url'   => $data['url'] ?: '-',
            'body'  => $data['body']
        ]);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->data['email'])
                    ->view('emails.bug_report');
    }
}
