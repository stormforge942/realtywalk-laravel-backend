<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class MagicLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        protected string $magicLink
    ) {}

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data['logo']       = url('images/logo-rw-horizontal.png', [], true);
        $data['greeting']   = __('mail.magic_link.greeting');
        $data['subject']    = __('mail.magic_link.subject');
        $data['introLines'] = __('mail.magic_link.lines');
        $data['actionUrl']  = $this->magicLink;
        $data['actionText'] = __('mail.magic_link.action_text');
        $data['outroLines'] = __('mail.magic_link.outro_lines', ['magic_link' => $this->magicLink]);

        return $this->subject($data['subject'])
                    ->view('emails.magic_link', $data);
    }
}
