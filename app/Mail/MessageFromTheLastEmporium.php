<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MessageFromTheLastEmporium extends Mailable
{
    use Queueable, SerializesModels;

    public $message;
    public $from;
    public $location;
    public $timestamp;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($message, $from, $location, $timestamp)
    {
        $this->message = $message;
        $this->from = $from;
        $this->location = $location;
        $this->timestamp = $timestamp;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->markdown('emails.message-from-the-last-emporium')
            ->message($this->message)
            ->with([
                'messageText' => $this->message, // the variable name 'message' conflicts with blade hint.
            ]);
    }
}
