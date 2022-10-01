<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MessageFromTheLastEmporium extends Mailable
{
    use Queueable, SerializesModels;

    private $fields;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($message, $from, $location, $timestamp)
    {
        $this->fields['message'] = $message;
        $this->fields['from'] = $from;
        $this->fields['location'] = $location;
        $this->fields['timestamp'] = $timestamp;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = str($this->fields['from'])->explode("\n")->map(fn ($line) => trim($line))->join(' ') .
            " from The Last Emporium, {$this->fields['location']}, {$this->fields['timestamp']->isoFormat('D MMM YYYY')}";

        return $this
            ->subject($subject)
            ->markdown('emails.message-from-the-last-emporium')
            ->with($this->fields);
    }
}
