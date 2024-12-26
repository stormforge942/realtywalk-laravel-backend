<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SimilarBuildersNotification extends Mailable
{
    use Queueable, SerializesModels;

    protected $builders;
    protected $todayAliases;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($builders, $todayAliases)
    {
        $this->builders = $builders;
        $this->todayAliases = $todayAliases;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.similar_builders', [
            'builders' => $this->builders,
            'todayAliases' => $this->todayAliases
        ]);
    }
}
