<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserRegistration extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        protected array $data,
        protected string $activationLink
    ) {}

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data['name'] = $this->data['name'];
        $data['subject'] = __('mail.registration.subject');
        $data['actionUrl']  = $this->activationLink;
        $data['actionText'] = __('mail.registration.action_text');
        $data['outroLines'] = __('mail.registration.outro_lines', [
            'activation_link' => $this->activationLink
        ]);

        return $this->subject($data['subject'])
                    ->view('emails.registration', $data);
    }
}
