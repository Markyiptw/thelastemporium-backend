<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Stevebauman\Purify\Facades\Purify;

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
        $this->fields['messageText'] = $message;
        $this->fields['from'] = $from;
        $this->fields['location'] = $location;
        $this->fields['timestamp'] = $timestamp->isoFormat('D MMM YYYY');

        $this->fields = collect($this->fields)
            ->map([Purify::class, 'clean'])
            ->all();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = str($this->fields['from'])->explode("\n")->map(fn ($line) => trim($line))->join(' ') .
            " from The Last Emporium, {$this->fields['location']}, {$this->fields['timestamp']}";

        return $this
            ->subject($subject)
            ->view('emails.message-from-the-last-emporium')
            ->with($this->fields);
    }
}
