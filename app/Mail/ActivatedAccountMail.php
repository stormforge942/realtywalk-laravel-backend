<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ActivatedAccountMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        protected User $user,
        protected ?string $password
    ) {}

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data['name'] = $this->user->name;
        $data['subject'] = __('mail.activation.subject');

        $lines_data = [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'password' => $this->password,
            'magic_login_url' => url('/users/signin'),
            'normal_login_url' => url('/users/signin-with-password')
        ];

        $data['lines'] = $this->password
            ? __('mail.activation.lines_with_password', $lines_data)
            : __('mail.activation.lines_without_password', $lines_data);

        return $this->subject($data['subject'])->view('emails.activation', $data);
    }
}
