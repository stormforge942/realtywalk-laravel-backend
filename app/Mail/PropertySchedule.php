<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class PropertySchedule extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $greeting;
    public $subject;
    public $introLines;
    public $logo;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        // $url = Storage::disk('Wasabi')->url('site/logo.png');
        $url = url('images/logo-rw-horizontal.png', [], true);

        $this->greeting = __('mail.property_schedule.greeting');
        $this->subject = __('mail.property_schedule.subject', [
            'user'    => $data['user'],
            'address' => $data['address']
        ]);
        $this->logo = $url;
        $this->introLines = $data['message'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.schedule');
    }
}
